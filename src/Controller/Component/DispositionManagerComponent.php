<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Cache\Cache;
use App\Model\Entity\Disposition;
use Cake\ORM\TableRegistry;
use Cake\Collection\Collection;
use DateTime;
use App\Form\AssignmentForm;

/**
 * CakePHP DispositionManagerComponent
 * @author dondrake
 */
class DispositionManagerComponent extends Component {
	
	public $components = ['EditionStack'];

	public $disposition;
	
	protected $controller;
	
	protected $SystemState;

	public function initialize(array $config) 
	{
		$this->controller = $this->_registry->getController();
		$this->SystemState = $this->controller->SystemState;
	}

	/**
	 * Read the current disposition or create a new one
	 * 
	 * The existance of the object generated (or pre-existing) will tip 
	 * the system into a new state that will effect all processes until 
	 * the disposition is either submitted or abandond.
	 * 
	 * It might seem to make sense to do this in initialize() so it's always ready, 
	 * but we only want this object when the user has initiated a disposition 
	 * creation process. 
	 * 
	 * @return Disposition The evolving or brand new disposition
	 */
	public function get() {
		$this->disposition = Cache::remember($this->SystemState->artistId(), [$this, 'generate'], 'dispo');
		return $this->disposition;
	}
	
	/**
	 * 
	 */
	public function discard() {
		Cache::delete($this->SystemState->artistId(), 'dispo');
	}
	
	/**
	 * Callable to make a brand new Disposition Entity
	 * 
	 * @return Disposition
	 */
	public function generate() {
		return new Disposition([
			'id' => NULL, 
			'label' => NULL,
			'type' => NULL,
			'complete' => TRUE,
			'start_date' => new DateTime('now'),
//			'end_date' => new DateTime(),
			'end_date' => NULL,
			'pieces' => [], 
			'member' => NULL, 
			'addresses' => [],
		]);
	}
	
	public function merge(Disposition $dispostion, array $arguments) {
//		osd($arguemnts);
		$this->_register($arguments);
		//die('dead in merge');
		$this->_setRedirect($arguments);
//		osd($this->disposition);die;
		$this->write();
		return $this->disposition;
	}
	
	public function write() {
		Cache::write($this->SystemState->artistId(), $this->disposition, 'dispo');
	}
	
	public function read() {
		return Cache::read($this->SystemState->artistId(), 'dispo');
	}
	
	/**
	 * 
	 * @param type $arguments
	 * @return 
	 */
	protected function _register($arguments) {
		if (array_key_exists('artwork', $arguments)) {
			$this->_registerArtwork($arguments);
		} elseif (array_key_exists('member', $arguments)) {
			$this->_registerMember($arguments);
		} elseif (array_key_exists('address', $arguments)) {
			$this->_registerAddress($arguments);
		}

	}
	
	/**
	 * Merge a piece into the disposition
	 * 
	 * Many scenarios
	 *	CREATE
	 *		Only format assignment can happen here because of view/tool filtering
	 *		Format with no valid pieces for the dispo type, flash message
	 *		New Format with many valid pieces, add format
	 *		New Format with one valid piece, add piece
	 *	REFINE
	 *		Format is already there, do nothing
	 *		New Format with many valid pieces, add format
	 *		New Format with one valid piece, add piece
	 *		(Format with no valid pieces prevented by view/tool filtering)
	 *		Piece is already there; do nothing
	 *		Piece is not there; add the piece
	 * 
	 * Additionally, this needs to reassign the piece if it's not in the 
	 * current format. And if the edition is an open, we'll be getting a value 
	 * indicating how many pieces to assign to the disposition and they may 
	 * also need to be moved to this format from another source.
	 * 
	 * @param type $arguments
	 */
	protected function _registerArtwork($arguments) {
//		die('register artwork');
		if (isset($arguments['piece'])) {
//			die('piece');
			$piece = $this->pieceStack($arguments['piece']);
			
			// piece was provided to register
			if ($this->disposition->indexOfPiece($arguments['piece']) === FALSE) {
//				die('not match');
				
				// nothing matches the id
				// this piece is not yet in the dispo 
				$this->_registerPiece($piece, $arguments);
				osd('add piece');
			} else {
//				die('some match');
				
				// something matching the piece id was found in the dispo
				// is the 'match' actually a format?
				$node = $this->disposition->returnPiece($arguments['piece']);
				if (!$node->fullyIdentified()) {
					
					// the match was a format, not a piece
					// does the format contain this piece (coincidentally with the same id)?
					if ($piece->edition_id === $node->edition_id) {
						
						// the match was a format that contains the piece with the same id. 
						// substitute the piece
						$this->_registerPiece($piece, $arguments);
						osd('sub piece for format');
					}
					// it was an unrealed format that happened to have the same id
					// this piece really is not in the dispo
					$this->_registerPiece($piece, $arguments);
					osd('add piece not format');
				}
				// the match was actually a piece. piece is already in dispo
			}
		} else { 
			// no piece was provided so presence of 'format' arg is assumed now
			$node = count($this->disposition->pieces) === 0 ? FALSE : $this->disposition->returnPiece($arguments['format']);
			if (!$node || $node->fullyIdentified()) {
				
				/**
				 * On CREATE we only just learned the dispo type and now, we may or 
				 * may not have valid pieces for the requested type in this format. 
				 * Additionally, we may have a unique piece or a format with only one 
				 * piece valid for this dispo. In those cases we'd want to set the 
				 * piece not the format.
				 */
				$format_stack = $this->formatStack($arguments['format']);
//				osd($format_stack);
				$this->disposition->pieces[] = $format_stack;
//				osd('add format');
//				$this->disposition->pieces[] = $this->formatStack($arguments['format']);
			}
		}
//		die('trying to return??');
//		osd($this->disposition);
	}
	
	/**
	 * Do the final storage of a piece int the dispo
	 * 
	 * Move it to the current format if necessary
	 * 
	 */
	protected function _registerPiece($piece, $arguments) {
//		osd($piece->format_id);
//		osd((integer) $arguments['format']);//die('register piece');
		if ($piece->format_id !== (integer) $arguments['format']) {
			$result = $this->_reassign($piece, $arguments['format']);
			if ($result === TRUE) {
				// update piece for accurate description
				$piece = $this->pieceStack($piece->id);
			} else {
				throw new \BadMethodCallException(print_r($result, TRUE));
			}
		}
		$this->disposition->pieces[] = $piece;
		$this->disposition->dropFormat($arguments['format']);
		return TRUE;
	}
	
	/**
	 * Do in-line piece reassignment during disposition
	 * 
	 * Formats show all appropriate pieces for the disposition, not just 
	 * those currently in the Format. So dispo may also establish the assignment. 
	 * The Edition::assign() process is leveraged for this. First build an array 
	 * that approximates the post data, establish the other environmental aspects, 
	 * then call for form validation and save. Errors are handled differently 
	 * from Edition::assign() because we're in so deep in a different process.
	 * 
	 * @param type $piece
	 * @param type $format_id
	 */
	private function _reassign($piece, $format_id) {
		// get the expected data environment
		$data = $this->EditionStack->stackQuery();
		extract($data); // providers, pieces
		$assignment = new AssignmentForm($providers);
		
		// hand create the POST data
		$this->request->data['destinations_for_pieces'] = "App\Model\Entity\Format\\$format_id";
		foreach($providers as $key => $provider) {
			// allowing the destination to be a source doesn't work right. 
			// and editions are never dispo destinations
			if ($provider->id != $format_id && $key !== 'edition') {
				$count = $key === 'edition' ? 0 : $key + 1;
				$this->request->data["source_for_pieces_$count"] = get_class($provider) . '\\' . $provider->id;
			}			
		}
		if (!$this->request->is('post')) {
			$this->request->data['to_move'] = $piece->number;
		}
		$this->request->data['to_move'] = (string)  $this->request->data['to_move'];
	
		// make the call to do the reassignment
		if ($assignment->execute($this->request->data)) {
			if($this->EditionStack->reassignPieces($assignment, $providers)) {
				return TRUE;
			} else {
				$errors = ['save_error' => __('There was a problem reassigning the pieces and '
						. 'since the requested piece(s) are not yet part of this format, '
						. 'they must be reassigned before being placed in the disposition. '
						. 'Please try again')];
			}

		} else {
			// have use correct input errors
			$errors= $assignment->errors();
		}
		return $errors;
	}
	
	/**
	 * Get a piece and its ancestors
	 * 
	 * @param integer $piece_id
	 * @return ResultObject
	 */
	public function pieceStack($piece_id) {
		$Pieces = TableRegistry::get('Pieces');
		return $Pieces->get($piece_id, [
			'contain' => 'Formats.Editions.Artworks',
		]);
	}

	/**
	 * Get a format and its ancestors
	 * 
	 * @param integer $format_id
	 * @return ResultObject
	 */
	public function formatStack($format_id) {
		$Formats = TableRegistry::get('Formats');
		$format = $Formats->get($format_id, [
			'contain' => ['Editions.Pieces','Editions.Artworks'],
//			'contain' => 'Editions.Artworks',
		]);
//		osd($format);
		return $format;
	}

	protected function _registerMember($arguments) {
		$Memebers = TableRegistry::get('Members');
		$conditions = $this->SystemState->buildConditions([]);
		$member = $Memebers->get($arguments['member'], ['conditions' => $conditions, 'contain' => ['Contacts', 'Addresses']]);
		$this->_mergeAddresses($member->addresses);
		unset($member->addresses);
		$this->disposition->member = $member;
	}
	
	protected function _registerAddress($arguments) {
		$Addresses = TableRegistry::get('Addresses');
		$conditions = $this->SystemState->buildConditions([]);
		$address = $Addresses->get($arguments['address'], ['conditions' => $conditions]);
		$this->disposition->addresses = [$address];
	}
	
	protected function _mergeAddresses($addresses) {
		$existing = FALSE;
		if (!empty($this->disposition->addresses)) {
			$existing = (new Collection($this->disposition->addresses))->map(
					function($address) {
				return $address->id;
			})->toArray();
		}
		if ((boolean) $existing) {
			$new_addresses = (new Collection($addresses))->reject(
				function($address) use($existing) {
					return in_array($address->id, $existing);
				})->toArray();
		} else {
			$new_addresses = $addresses;
		}
		$this->disposition->addresses = array_merge($this->disposition->addresses, $new_addresses);
	}

	protected function _setRedirect($arguments) {
		if (array_key_exists('artwork', $arguments)) {
			$this->SystemState->referer([
				'controller' => 'artworks',
				'action' => 'review',
				'?' => $arguments]);
		}
	}
	
	/**
	 * Insure the Artworks have pieces that can satisfy the disposition
	 * 
	 */
	public function validatePieces() {
	}
	
}

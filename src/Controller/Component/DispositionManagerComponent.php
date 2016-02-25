<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Cache\Cache;
use App\Model\Entity\Disposition;
use Cake\ORM\TableRegistry;
use Cake\Collection\Collection;
use DateTime;

/**
 * CakePHP DispositionManagerComponent
 * @author dondrake
 */
class DispositionManagerComponent extends Component {
	
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
			return $this->_registerArtwork($arguments);
		} elseif (array_key_exists('member', $arguments)) {
			return $this->_registerMember($arguments);
		} elseif (array_key_exists('address', $arguments)) {
			return $this->_registerAddress($arguments);
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
	 * @param type $arguments
	 */
	protected function _registerArtwork($arguments) {
//		osd($arguments);//die;
		if (isset($arguments['piece'])) {
//			osd('piece');
			if ($this->disposition->indexOfPiece($arguments['piece']) === FALSE) {
//				osd('not there');
				// piece is not there
				$this->disposition->pieces[] = $this->pieceStack($arguments['piece']);
				$this->disposition->dropFormat($arguments['format']);
			} else {
				// is the 'match' actually a format?
				$node = $this->disposition->returnPiece($arguments['piece']);
				if (!$node->fullyIdentified()) {
					// and does the format contain this piece (coincidentally with the same id)?
					$piece = $this->pieceStack($arguments['piece']);
					if ($piece->edition_id === $node->edition_id) {
						$this->disposition->pieces[] = $this->pieceStack($arguments['piece']);
						$this->disposition->dropFormat($arguments['format']);
					}
				}
				// piece is already there
			}
		} else { // presence of 'format' arg is assumed now
			$node = empty($this->disposition->pieces) ? FALSE : $this->disposition->returnPiece($arguments['format']);
			if (!$node || $node->fullyIdentified()) {
				
				/**
				 * On CREATE we only just learned the dispo type and now, we may or 
				 * may not have valid pieces for the requested type in this format. 
				 * Additionally, we may have a unique piece or a format with only one 
				 * piece valid for this dispo. In those cases we'd want to set the 
				 * piece not the format.
				 */
				$this->disposition->pieces[] = $this->formatStack($arguments['format']);
			}
		}
//		osd($this->disposition);
	}
	
	public function pieceStack($piece_id) {
		$Pieces = TableRegistry::get('Pieces');
		return $Pieces->get($piece_id, [
			'contain' => 'Formats.Editions.Artworks',
		]);
	}

	public function formatStack($format_id) {
		$Pieces = TableRegistry::get('Formats');
		return $Pieces->get($format_id, [
			'contain' => 'Editions.Artworks',
		]);
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

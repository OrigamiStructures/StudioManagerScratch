<?php
namespace App\Controller\Component;

use App\Model\Lib\ContextUser;
use App\Model\Table\EditionsTable;
use App\Model\Table\FormatsTable;
use App\Model\Table\PiecesTable;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Collection\Collection;
use App\Model\Entity\Piece;
use Cake\I18n\Time;
use Cake\Cache\Cache;
use App\Lib\Traits\EditionStackCache;
use App\Model\Lib\Providers;
use App\Lib\EditionTypeMap;
use Cake\Utility\Hash;

/**
 * EditionStackComponent provides a unified interface for the three layers, Edition, Format and Piece
 *
 * Managing Pieces within Editions and their Formats requires complex data
 * objects and collections. This component localizes these processes and provides
 * tools required by the three controllers as they collaborate to maintain
 * edition content. The actual movement of Pieces across the Edition/Format
 * layers is passed of to a separate component.
 *
 * @todo Exception in this or calling code should clear the edition stack cache, probably
 *			a special Exception class should be written that takes care of the cache.
 *
 * @todo This Component seems to mingle PieceTable tasks and AssignmentForm services
 * The logic of having much of this code in this component is suspect. Many parts
 * could be in the PieceTable class. The stackQuery() data could be extracted by
 * and returned from ArtworkStack::stackQuery(); either as optional return data or
 * through a separate method in that class. And even that Component might be
 * better as a Model class.
 *
 * @author dondrake
 */
class EditionStackComponent extends Component {

	use EditionStackCache;

	protected $pieces_to_save ;
	protected $pieces_to_delete;
	
	/**
	 * Move the indicated pieces to to indicated destination
	 *
	 * All request inputs were valid and logical we now have the  properties for the edit
	 * <pre>
	 *	- the set source piece entities ($assignment->source_pieces)
	 *  - an idetifier for the destination ($assignment->destination)
	 *  - if edition is OPEN
	 *		- the number of pieces to move ($assignment->source_quantity)
	 *  - if edition is LIMITED
	 *		- the list of piece number to move ($assignment->$source_numbers)
	 * </pre>
	 *
	 * @param Form $assignment The Form that gathered and validated the request
	 * @param array $providers The oringal list of Edition/Formats and their pieces
	 */
	public function reassignPieces($assignment, $providers) {
		$edition = $providers['edition'];
		$split = preg_split('/\\\/', $assignment->destination);
		if (stristr($split[count($split)-2], 'Format')) {
			$patch = ['format_id' => $split[count($split)-1]];
		} else {
			$patch = ['format_id' => NULL];
		}

		if (EditionTypeMap::isNumbered($edition->type)) {
			$this->_prepareNumberedPieces($assignment, $patch);
		} else {
			$this->_prepareOpenPieces($assignment, $patch, $edition->id);
		}
		// perform transactional save/delete
		return $this->reassignmentTransaction();
	}

	/**
	 *
	 * @param Form $assignment The Form that gathered and validated the request
	 * @param array $providers The oringal list of Edition/Formats and their pieces
	 * @param array $patch
	 * @return
	 */
	protected function _prepareNumberedPieces(\App\Form\AssignmentForm $assignment, $patch) {
		// filter the source by the request
		$Pieces = TableRegistry::getTableLocator()->get('Pieces');
		$source = new Collection($assignment->source_pieces);

		$to_move = $source->filter(function($value) use($assignment) {
			return in_array($value->number, $assignment->request_numbers);
		});

		$this->pieces_to_save = $to_move->map(function($value) use($patch, $Pieces) {
			return $Pieces->patchEntity($value, $patch);
		})->toArray();

		// if a move was from a Format to the Edition, the Formats counter cache will
		// fail. Move one piece from each format that has one to correct them.
		if (stristr($assignment->destination, 'Edition')) {
			$this->_getFormatTriggerPieces($assignment);
		}

		return $this->pieces_to_save;

	}

// https://github.com/OrigamiStructures/StudioManagerScratch/issues/63
// and issue 24
	// try to elimate with an event triggered by controller after save
	public function _getFormatTriggerPieces(\App\Form\AssignmentForm $assignment) {
		$Pieces = TableRegistry::getTableLocator()->get('Pieces');
		$update_trigger_value = [
			'created' => new \DateTime('now')
		];
		$formats = $assignment->_providers->formats;
		$trigger_pieces = (new Collection($formats))
			->reduce(function($accumulator, $format) use ($Pieces, $update_trigger_value){
				if (!empty($format->fluid)) {
					$piece = $Pieces->patchEntity($format->fluid[0], $update_trigger_value);
					$accumulator[] = $piece;
				}
				return $accumulator;
			}, []);

		$this->pieces_to_save = $this->pieces_to_save + $trigger_pieces;
	}

	/**
	 * Move a quantity of OpenEdition pieces from source(s) to a destination
	 *
	 * During reassignment multiple sources might be indicated. They will have
	 * avaialble quantity drawn unitl the move request is satisfied
	 *
	 * @param FormObject $assignment
	 * @param array $patch
	 * @param integer $edition_id
	 * @return array
	 * @throws \Cake\Network\Exception\BadRequestException
	 */
	protected function _prepareOpenPieces($assignment, $patch, $edition_id) {

		$this->pieces_to_save = $pieces = $assignment->source_pieces;
		$change = $assignment->request_quantity;

//		osd($pieces);
		/**
		 * I lifted this algorithm from
		 * PieceAllocationComponent::decreaseOptionEdition()
		 * Refactoring might help, or this separation might be ok.
		 */
		$index = 0;
		$limit = count($pieces);
		$deletions = [];
		do {
			$piece = $pieces[$index++];

			if ($piece->quantity > $change) {
				$piece->quantity -= $change;
				$change = 0;
//				osd(['pq'=>$piece->quantity, 'c'=>$change],'quantity > $change');
			} else { // change >= quantity
				$change -= $piece->quantity;
				$piece->quantity = 0;
				$deletions[] = $piece;
				unset($this->pieces_to_save[$index -1]); // this line was added to the lifted code
//				osd(['pq'=>$piece->quantity, 'c'=>$change],'quantity <= $change');
			}

		} while ($change > 0 && $index < $limit);

		if ($change > 0) {
			throw new \Cake\Network\Exception\BadRequestException(
				'There were not enough undisposed Pieces to move all the requested pieces'); // this message was changed from the lifted code
		}
		// END OF LIFTED CODE

		//make the new enitity
		if(is_null($assignment->destination_piece)) {
			$piece_entity = new Piece([
				'quantity' => $assignment->request_quantity,
				'edition_id' => $edition_id,
				'user_id' => $this->SystemState->artistId(),
			]);
		} else {
			$piece_entity = $assignment->destination_piece;
			$patch['quantity'] = $piece_entity->quantity + $assignment->request_quantity;
		}

		$PieceTable = TableRegistry::getTableLocator()->get('Pieces') ;
		$this->pieces_to_save[] = $PieceTable->patchEntity($piece_entity, $patch);
		$this->pieces_to_delete = $deletions;

		return $this->pieces_to_save;

	}

		/**
	 * Wrap both refinement save and deletions in a single transaction
	 *
	 * Creation is a simple Table->save() but refinement may involve deletion
	 * of piece records. This method provides refinement for all layers of the stack.
	 *
	 * @param Entity $artwork
	 * @param array $deletions
	 * @return boolean
	 */
	public function reassignmentTransaction() {
		$PiecesTable = TableRegistry::getTableLocator()->get('Pieces');
		Cache::delete("get_default_artworks[_{$this->SystemState->queryArg('artwork')}_]", 'artwork');//die;
		$result = $PiecesTable->getConnection()->transactional(function () use ($PiecesTable) {
			$result = TRUE;
			if (is_array($this->pieces_to_save)) {
				foreach ($this->pieces_to_save as $piece) {
					$step = $PiecesTable->save($piece, ['atomic' => false, 'checkRules' => false]);
					$result = $result && $step;
				}
			}
			if (is_array($this->pieces_to_delete)) {
				foreach ($this->pieces_to_delete as $piece) {
					$result = $result && $PiecesTable->delete($piece, ['atomic' => false]);
				}
			}
			return $result;
		});
		return $result;
	}



}

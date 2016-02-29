<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Collection\Collection;
use App\Model\Entity\Piece;
use Cake\I18n\Time;
use Cake\Cache\Cache;

/**
 * EditionStackComponent provides a unified interface for the three layers, Edition, Format and Piece
 * 
 * Managing Pieces within Editions and their Formats requires complex data 
 * objects and collections. This component localizes these processes and provides 
 * tools required by the three controllers as they collaborate to maintain 
 * edition content. The actual movement of Pieces across the Edition/Format 
 * layers is passed of to a separate component.
 * 
 * @author dondrake
 */
class EditionStackComponent extends Component {
	
	protected $pieces_to_save ;
	protected $pieces_to_delete;


	public function initialize(array $config) 
	{
		$this->controller = $this->_registry->getController();
		$this->SystemState = $this->controller->SystemState;
	}

	/**
	 * Return the object representing an Edition and its contents down through Pieces, and the full Piece set
	 * 
	 * The Edition carries its Artwork record for some upstream context. 
	 * The Edition and its Formats are returned as siblings, each with its Pieces. 
	 * The Pieces are categorized as appropriate to that layer. 
	 * The AssignemntTrait on the Edition and Piece entities unify piece access. 
	 * <pre>
	 * // providers array
	 * ['edition' => EditionEntity {
	 *			..., 
	 *			'unassigned' -> PiecesEntity {},
	 *      },
	 *  0 => FormatEntity {
	 *			...,
	 *			'fluid' -> PiecesEntity {},
	 *      },
	 *  ...
	 *  0+n => FormatEntity {
	 *			...,
	 *			'fluid' -> PiecesEntity {},
	 *      },
	 * ]
	 * 
	 * // pieces array
	 * [0 => PieceEntity {},
	 * ...
	 * 0+n => PieceEntity {},
	 * ]
	 * </pre>
	 * 
	 * 
	 * @return tuple 'providers, pieces'
	 */
	public function stackQuery() {
		$Pieces = TableRegistry::get('Pieces');
		$Formats = TableRegistry::get('Formats');
		$Editions = TableRegistry::get('Editions');
		
		$edition_condition = $this->SystemState->buildConditions(['edition' => 'id']);	
		$child_condition = $this->SystemState->buildConditions(['edition']);
//		
		$edition = $Editions->find()
				->where($edition_condition)
//				->contain('Format')
				->toArray()[0];
		//osd($edition);
		$unassigned = $Pieces->find('unassigned', $child_condition);
		$edition->unassigned = $unassigned->toArray();
		
		$formats = $Formats->find()->where($child_condition);
		$formats = $formats->each(function($format) use($child_condition, $Pieces) {
			$conditions = $child_condition + ['format_id' => $format->id];
			$format->fluid = $Pieces->find('fluid', $conditions)->toArray();
		});

		$providers = ['edition' => $edition] + $formats->toArray();
		
		// this may need ->order() later for piece-table reporting of open editions
		$pieces = $Pieces->find()->where($child_condition); 
		
		return ['providers' => $providers, 'pieces' => $pieces];
				
	}
	
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
		preg_match('/(.*)(\d+)/', $assignment->destination, $match);
		if (stristr($match[1], 'Format')) {
			$patch = ['format_id' => $match[2]];
		} else {
			$patch = ['format_id' => NULL];
		}
		
		if (\App\Lib\SystemState::isNumberedEdition($edition->type)) {
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
		$Pieces = TableRegistry::get('Pieces');
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
	
	public function _getFormatTriggerPieces(\App\Form\AssignmentForm $assignment) {
		$Pieces = TableRegistry::get('Pieces');
		$update_trigger_value = [
			'created' => new \DateTime('now')
		];
		$formats = $assignment->_providers;
		array_shift($formats);
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

		$PieceTable = TableRegistry::get('Pieces') ;
		$this->pieces_to_save[] = $PieceTable->patchEntity($piece_entity, $patch);
		$this->pieces_to_delete = $deletions;

		return $this->pieces_to_save;

	}
	
	protected function searchExistingPiece($assignment) {
		
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
		$PiecesTable = TableRegistry::get('Pieces');
		Cache::delete("get_default_artworks[_{$this->SystemState->queryArg('artwork')}_]", 'artwork');//die;
		$result = $PiecesTable->connection()->transactional(function () use ($PiecesTable) {
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

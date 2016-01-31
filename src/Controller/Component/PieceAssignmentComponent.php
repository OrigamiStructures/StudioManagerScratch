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
use CakeDC\Users\Exception\BadConfigurationException;

/**
 * CakePHP PieceAssignment
 * @author dondrake
 */
class PieceAssignmentComponent extends Component {
	
	protected $controller;
	protected $SystemState;
	protected $stack;
	protected $artwork;
	protected $Artworks;
	private $edition;
	private $edition_index;
	private $format;
	private $format_index;
	private $multiple_formats;
	private $pieces;
	
	public function initialize(array $config = array()) {
		parent::initialize($config);
		$this->controller = $this->_registry->getController();
		$this->SystemState = $this->controller->SystemState;

		$this->artwork = $config['artwork'];
		if (isset($this->artwork->multiple)) {
			$this->multiple_formats = (boolean) $this->artwork->multiple; // an input value from creation forms
		}
//		$this->stack = $this->stackCounts($this->artwork->id);
//		$this->mostRecentEdition();
//		$this->mostRecentFormat();
	}
	
	public function assign() {
		$index = array_keys($this->artwork->editions)[0];
		$this->edition = $this->artwork->editions[$index];
		unset($this->pieces);
		if ($this->SystemState->is(ARTWORK_CREATE) && ($this->onePiece() || !$this->multiple_formats)) {
			$this->piecesToFormat();
		}
	}
	
	/**
	 * Move the single piece onto the last edited format
	 * 
	 * THIS SEEMS A BIT QUESTIONABLE. ONE PIECE? FORMAT ASSUMPTION...
	 * 
	 * @param Entity $piece
	 * @return Entity
	 */
	protected function piecesToFormat($piece = NULL) {
		$edition = $this->artwork->editions[0];
		$format = $edition->formats[0];
		$format->pieces = $edition->pieces;
		unset($edition->pieces);
	}

	/**
	 * Does the edition have only one piece?
	 * 
	 * @return boolean
	 */
	protected function onePiece() {
		return $this->edition->quantity === 1;
	}

	/**
	 * Handle edition size changes for editions that already have pieces
	 * 
	 * There is a lot of initialization that has been done. Many properties set
	 * 
	 * @param Entity $artwork The post-save entity
	 * @param array $quantity_tuple (int) $original, (int) $refinement, $id
	 */
	public function refine($data = []) {
		extract($data); // $original, $refinement, $id (edition_id)
		$this->edition = $this->artwork->returnEdition($id);
		$change = $refinement - $original; // decrease (-x), increase (+x)
		
		// Unique and Rights have ONE piece don't have the input for quanitity
		if ($this->edition->type === EDITION_OPEN) {
			$method = 'resizeOpenEdition';
		} else {
			$method = 'resizeLimitedEdition';
		}
		return $this->$method($original, $change); // return array of Pieces to delete
	}
	
	/**
	 * Change the size of an Open edition
	 * 
	 * @param integer $original
	 * @param integer $change
	 */
	protected function resizeOpenEdition($original, $change) {
		// both increase and decrease will need to do queries
		$this->Pieces = TableRegistry::get('Pieces');
		$piece = $this->Pieces->find('unassigned', ['edition_id' => $this->edition->id])->toArray();

		if ($change > 0) {
			return $this->increaseOpenEdition($change, $piece); // return [] (deletions required)
		} else {
			
			$editions = TableRegistry::get('Editions');
			$original_edition = $editions->get($this->edition->id, ['contain' => ['Formats']]);

			if (abs($change) > $original_edition->undisposed_piece_count ) {
				$this->edition->errors('quantity', 'The quantity was set lower than the allowed minimum');
				return;
			}
			return $this->decreaseOpenEdition($change, $original_edition); // return [] deletions required
		}
	}
	
	/**
	 * Change the size of a limited edition
	 * 
	 * @param integer $original
	 * @param signed-integer $change
	 * @return type
	 */
	protected function resizeLimitedEdition($original, $change) {
		if ($change > 0) {
			return $this->increaseLimitedEdition($change); // return [] (deletions required)
		} else {
			
			$editions = TableRegistry::get('Editions');
			$original_edition = $editions->get($this->edition->id, ['contain' => ['Formats']]);

			$pieces = TableRegistry::get('Pieces');
			$highestNumberDisposed = $pieces->highestNumberDisposed(['edition_id' => $this->edition->id]);
			$edition_tail = $original_edition->quantity - $highestNumberDisposed;
						
			if (abs($change) > $edition_tail ) {
				$this->edition->errors('quantity', 'The quantity was set lower than the allowed minimum');
				return;
			}
			return $this->decreaseLimitedEdition($change, $pieces, $original_edition); // return [] deletions required
		}
		osd('resizeLimitedEdition');//die;
	}
	
	/**
	 * Add pieces to an existing Open edition type
	 * 
	 * Open editions use the 'quantity' field of Piece entities, so if an 
	 * open edition has unassigned pieces, there will be one record for them. 
	 * Increasing the edition size requires changing the 'quantity' value 
	 * in that record or creating an unassigned piece record with the quantity
	 * 
	 * @param integer $change Number of additional unassigned pieces needed
	 * @param entity $piece Entity, the unassigned piece record
	 * @throws BadConfigurationException
	 */
	protected function increaseOpenEdition($change, $piece) {
		// hasUnassigned() can't work because the edition is in flux 
		// and values aren't updated. Specifically, edition->quantity which 
		// calculates unassigned is now out of phase with pieces (that's why we're here)
		$format_id = FALSE;
		$flat_edition = $this->edition->format_count === 1;
		
		if ($flat_edition) {
			$format_id = $this->edition->formats[0]->id;
			$piece = $this->Pieces->find('fluid', 
				[
					'edition_id' => $this->edition->id,
				])->toArray();
		} else {
			$piece = $this->Pieces->find('unassigned', 
				['edition_id' => $this->edition->id])->toArray();
		}
		
		if (count($piece) === 1) {
			$piece = $piece[0];
			$data = [
				'quantity' => $piece->quantity + $change,
				'format_id' => $flat_edition ? $format_id : NULL,
			];
			$this->edition->pieces = [$this->Pieces->patchEntity($piece, $data)];

		} elseif (empty($piece)) {
			$data = [
				'quantity' => $change,
				'format_id' => $flat_edition ? $format_id : NULL,
			];
			$this->edition->pieces = [new Piece(
				$this->Pieces->spawn(OPEN_PIECES, 1, $data)[0]
			)];
		
		} else {
			\Cake\Log\Log::emergency('More than one unassigned piece was '
					. 'found on an Open edition', $piece);
			throw new BadConfigurationException(
					"Open Edition types should not have more than one unassigned "
					. "piece record but edition {$this->edition->id} has more.");
		}
		
		return []; // deletions required
	}
	
	/**
	 * 
	 * @param type $change
	 * @param type $piece
	 */
	protected function decreaseOpenEdition($change, $original_edition) {
		$change = abs($change);
		$pieces = $this->Pieces->find('undisposed', 
			[
				'edition_id' => $this->edition->id,
			])->order(['format_id' => 'ASC'])->toArray();
//		osd($piesces);die;

		(new Collection($this->edition->formats))->each(function($format) {
			$format->pieces = NULL;
			$format->dirty('pieces', FALSE);
		}) ;
		$this->edition->pieces = $pieces;
		
		$index = 0;
		$limit = count($pieces);
		do {
			$piece = $pieces[$index++];
			$deletions = [];
			if ($piece->quantity > $change) {
				$piece->quantity -= $change;
				$change = 0;
			} else { // change >= quantity
				$change -= $piece->quantity;
				$piece->quantity = 0;
				$deletions[] = $piece;
			}
			
		} while ($change > 0 && $index < $limit);
		
		if ($change > 0) {
			throw new \Cake\Network\Exception\BadRequestException(
				'There were not enough undisposed Pieces to reduce the Edition the '
					. 'requested amount.');
		}

		return $deletions;
	}
	
	protected function increaseLimitedEdition($change) {

		$editions = TableRegistry::get('Editions');
		$original_edition = $editions->get($this->edition->id, ['contain' => ['Formats']]);
		$flat_edition = $this->edition->format_count === 1;
//		(new Collection($this->edition->formats))->each(function($format) {
//			$format->pieces = NULL;
//			$format->dirty('pieces', FALSE);
//		}) ;
//		$this->edition->pieces = [];
		
		// if flat, add to the one format,
//		if ($flat_edition) {
//			$format_id = $this->edition->formats[0]->id;
//		}
		
		$data = [
			'quantity' => 1,
			'edition_id' => $this->edition->id,
//			'format_id' => $flat_edition ? $format_id : NULL,
		];
		$this->Pieces = TableRegistry::get('Pieces');
		$new_pieces = $this->Pieces->spawn(NUMBERED_PIECES, $change, $data, $original_edition->quantity);
//		osd($new_pieces);
		$new_pieces = (new Collection($new_pieces))->map(function($piece) {
			return (new Piece($piece));
		});
		if ($flat_edition) {
			$this->edition->formats[0]->pieces = $new_pieces->toArray();
		} else {
			$this->edition->pieces = $new_pieces->toArray();
		}
//		osd($this->edition); die;
		return [];
	}
	
	protected function decreaseLimitedEdition($change, $pieces, $original_edition) {
		$change = abs($change);
		$deletions = $pieces->find()->where(['edition_id' => $this->edition->id])
				->order(['number' => 'DESC'])
				->limit($change);
		return $deletions->toArray();
	}
	
}

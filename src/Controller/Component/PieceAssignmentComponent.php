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
		$this->Artworks = TableRegistry::get('Artworks');
		$this->artwork = $config['artwork'];
		if (isset($this->artwork->multiple)) {
			$this->multiple_formats = (boolean) $this->artwork->multiple;
		}
		$this->stack = $this->stackCounts($this->artwork->id);
		$this->mostRecentEdition();
		$this->mostRecentFormat();
	}
	
	public function assign() {
		unset($this->pieces);
		if ($this->SystemState->is(ARTWORK_CREATE) && $this->onePiece()) {
			$this->pieces = [$this->piecesToFormat($this->edition->pieces[0])];
		} elseif ($this->SystemState->is(ARTWORK_CREATE) &&  !$this->multiple_formats ) {
			$this->pieces = (new Collection($this->edition->pieces))
					->map([$this, 'piecesToFormat'])->toArray();
		}
		if (isset($this->pieces)) {
			$this->Formats = \Cake\ORM\TableRegistry::get('Formats');
			$this->format->pieces = $this->pieces;
			$this->format->dirty('pieces', true);
			$this->Formats->save($this->format);
		}
		/**
		 * Open editions --
		 * Pieces don't get moved to formats because there is initially only 
		 * one piece attached to the Edition. In truth, this one record may 
		 * not be required at all. It may be true that the assignment of 
		 * a piece to an Open Format involves adding a new piece to the 
		 * system and setting its quantity. In a Limited, the availble pool
		 * of unassigned pieces determines the possiblity of a move. For 
		 * an open, the difference between Edition->quantity and 
		 * Edition->assigned_piece_count determines. This suggests the need 
		 * for Enity sub-classes that can answer the limit question on 
		 * a single common call.
		 */
	}
	
	
	/**
	 * Move the single piece onto the last edited format
	 * 
	 * THIS SEEMS A BIT QUESTIONABLE. ONE PIECE? FORMAT ASSUMPTION...
	 * 
	 * @param Entity $piece
	 * @return Entity
	 */
	protected function piecesToFormat($piece) {
			$piece->format_id = $this->format->id;
			$piece->dirty('format_id', true);
			return $piece;
	}

		/**
	 * Get an analysis stack for an Artwork
	 * 
	 * This will report on number of associated records at each level and 
	 * allow the most recently edited record to be detected. 
	 * 
	 * @param int $artwork_id
	 * @return Entity
	 */
	protected function stackCounts($artwork_id) {
		
		$stackCounts = $this->Artworks->get($artwork_id, [
			'fields' => ['id', 'edition_count'],
			'contain' => [
				'Editions' => ['fields' => [
					'id', 'modified', 'artwork_id', 'type', 'format_count', 'quantity', 'assigned_piece_count']],
				'Editions.Pieces' => [ 'fields' => [
					'id', 'edition_id', 'format_id', 'number', 'quantity', 'disposition_count']],
				'Editions.Formats' => ['fields' => [
					'id', 'modified', 'edition_id', 'assigned_piece_count']],
				'Editions.Formats.Pieces' => [ 'fields' => [
					'id', 'edition_id', 'format_id', 'number', 'quantity', 'disposition_count']],
			]
		]);
		return $stackCounts;
	}
	
	protected function mostRecentEdition() {
		if (isset($this->edition)) {
			return $this->edition;
		}
		if (count($this->artwork->editions) === 1 && $this->artwork->edition_count === 1) {
			$this->edition_index = 0;
			$this->edition = $this->artwork->editions[0];
		} else {
			$this->edition = (new Collection($this->stack->editions))->max(
				function($edition) {return $edition->modified->toUnixString();
			});
			$this->edition_index = $this->artwork->indexOfEdition($this->edition->id);
		}
		return $this->edition;
	}
	
	protected function onePiece() {
		return $this->edition->quantity === 1;
	}

	protected function mostRecentFormat() {
		if (isset($this->format)) {
			return $this->format;
		}
		$this->mostRecentEdition();
		if (count($this->edition->formats) === 1 || $this->edition->format_count === 1) {
			$this->format_index = 0;
			$this->format = $this->edition->formats[0];
		} else {
			if (!isset($this->edition)) {
			}
			$formats = new Collection($this->edition->formats);
//			osd($formats->max('modified'));
			$this->format = (new Collection($this->edition->formats))->max(
				function($format) {return $format->modified->toUnixString();
			});
			// possibly not needed?
			$this->forat_index = $this->edition->indexOfFormat($this->format->id);
		}
		return $this->format;
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
//		$this->Pieces = TableRegistry::get('Pieces');
//		$piece = $this->Pieces->find('unassigned', ['edition_id' => $this->edition->id])->toArray();

		if ($change > 0) {
		$this->Pieces = TableRegistry::get('Pieces');
		$piece = $this->Pieces->find('unassigned', ['edition_id' => $this->edition->id])->toArray();
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
	
	protected function resizeLimitedEdition($original, $change) {
		if ($change > 0) {
			return $this->increaseLimitedEdition($change); // return [] (deletions required)
		} else {
			
			$editions = TableRegistry::get('Editions');
			$original_edition = $editions->get($this->edition->id, ['contain' => ['Formats']]);

			$pieces = TableRegistry::get('Pieces');
			$highestNumberDisposed = $pieces->highestNumberDisposed(['edition_id' => $this->edition->id]);
			$edition_tail = $original_edition->quantity - $highestNumberDisposed['number'];
						
			if (abs($change) > $edition_tail ) {
				$this->edition->errors('quantity', 'The quantity was set lower than the allowed minimum');
				return;
			}
			return $this->decreaseLimitedEdition($change, $pieces, $original_edition); // return [] deletions required
		}
		osd('resizeLimitedEdition');//die;
	}
	
	protected function increaseLimitedEdition($change) {

		$editions = TableRegistry::get('Editions');
		$original_edition = $editions->get($this->edition->id, ['contain' => ['Formats']]);
		$flat_edition = $this->edition->format_count === 1;
		(new Collection($this->edition->formats))->each(function($format) {
			$format->pieces = NULL;
			$format->dirty('pieces', FALSE);
		}) ;
		$this->edition->pieces = [];
		
		// if flat, add to the one format,
		if ($flat_edition) {
			$format_id = $this->edition->formats[0]->id;
		}
		
		$data = [
			'quantity' => 1,
			'edition_id' => $this->edition->id,
			'format_id' => $flat_edition ? $format_id : NULL,
		];
		$this->Pieces = TableRegistry::get('Pieces');
		$new_peices = $this->Pieces->spawn(NUMBERED_PIECES, $change, $data, $original_edition->quantity);
		$new_peices = (new Collection($new_peices))->map(function($piece) {
			return (new Piece($piece));
		});
		$this->edition->pieces = $new_peices->toArray();

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

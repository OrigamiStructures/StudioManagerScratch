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
	
	public function initialize(array $config = array()) {
		parent::initialize($config);
		$this->controller = $this->_registry->getController();
		$this->SystemState = $this->controller->SystemState;
		$this->Artworks = TableRegistry::get('Artworks');
		$this->artwork = $config['artwork'];
		$this->stack = $this->stackCounts($this->artwork->id);
		$this->mostRecentEdition();
		$this->mostRecentFormat();
//		osd($this->artwork);
//		osd($this->stack);
//		osd($this->mostRecentEdition());
//		osd($this->mostRecentFormat());
//		die;
	}
	
	public function assign() {
		if ($this->SystemState->is(ARTWORK_CREATE) && $this->onePiece() ) {
			$this->Pieces = \Cake\ORM\TableRegistry::get('Pieces');
			$this->pieces = $this->edition->pieces[0];
			$this->pieces->format_id = $this->format->id;
			$this->pieces->dirty('format_id', true);
			$this->Pieces->save($this->pieces);
		} 
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
					'id', 'modified', 'artwork_id', 'format_count', 'quantity', 'assigned_piece_count']],
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
			osd($formats->max('modified'));
			$this->format = (new Collection($this->edition->formats))->max(
				function($format) {return $format->modified->toUnixString();
			});
			// possibly not needed?
			$this->forat_index = $this->edition->indexOfFormat($this->format->id);
		}
		return $this->format;
	}

	public function hasOnePiece() {
		return $this->stack->edition->quantity == 1;
	}
	
	public function isFlat() {
		return ($this->stack->edition_count == 1) && 
			($this->stack->edtions[0]->format_count == 1);
	}
}
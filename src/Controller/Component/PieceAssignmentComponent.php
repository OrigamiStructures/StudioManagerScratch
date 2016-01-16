<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * CakePHP PieceAssignment
 * @author dondrake
 */
class PieceAssignmentComponent extends Component {
	
	public $stack;
	public $Artworks;
	
	public function initialize(array $config = array()) {
		parent::initialize($config);
		$this->Artworks = TableRegistry::get('Artworks');
		$this->stack = $this->stackCounts($config['artwork_id']);
	}
	
	public function stackCounts($artwork_id) {
		
		$stackCounts = $this->Artworks->get($artwork_id, [
			'fields' => ['id', 'edition_count'],
			'contain' => [
				'Editions' => ['fields' => [
					'id', 'artwork_id', 'format_count', 'quantity', 'assigned_piece_count']],
				'Editions.Formats' => ['fields' => [
					'id', 'edition_id', 'assigned_piece_count']]
			]
		]);
		return $stackCounts;
	}

	public function hasOnePiece() {
		return $this->stack->edition->quantity == 1;
	}
	
	public function isFlat() {
		return ($this->stack->edition_count == 1) && 
			($this->stack->edtions[0]->format_count == 1);
	}
}

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
		$edition = $Editions->find()->where($edition_condition)->toArray()[0];
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
}

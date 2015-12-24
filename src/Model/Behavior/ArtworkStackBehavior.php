<?php

/*
 * Copyright 2015 Origami Structures
 */

namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use App\Model\Table\ArtworksTable;
use App\Model\Table\EditionsTable;
use App\Model\Table\FormatsTable;
use App\Model\Table\PiecesTable;
use App\Model\Entity\Artwork;
use App\Model\Entity\Edition;
use App\Model\Entity\Format;
use App\Model\Entity\Piece;
use Cake\Utility\Inflector;

/**
 * CakePHP ArtworkStackBehavior
 * @author jasont
 */
class ArtworkStackBehavior extends Behavior {
    public $Artwork = FALSE;
    public $Edition = FALSE;
    public $Format = FALSE;
    public $Piece = FALSE;
	
	protected $_created;

	public $stack_members = [
        'Artwork', // Edition
//		'Series', // Editions
        'Edition', // Format, Piece
//		'Subscription', // Format
        'Format', // Piece
        'Piece', //
    ];
    
    public function __construct(\Cake\ORM\Table $table, array $config = array()) {
        parent::__construct($table, $config);
    }
    
    public function saveStack($data) {
		$this->success = TRUE;
        $this->setupEntities($data);
		// start transaction
        foreach ($this->stack_members as $entity) {
            $alias = Inflector::pluralize($entity);
            $table = \Cake\ORM\TableRegistry::get($alias);
			
			// Getting an id might be better than saving because the 
			// entity retains its unsaved settings. Edition especially 
			// needs to retain this info so Pieces can be properly made.
//			if ($this->success && $table->save($this->$entity)) {
			if (true) {
//				osd($this->$entity->id, 'id');
				$this->updateAssociations($table);
			} else {
				// rollback transaction
				 return false;
			}
        }
		return true;
    }
    
	/**
	 * Generate the Entities represented in the data
	 * 
	 * $data must have top level indexes that match the names of Entity 
	 * classes. The array found at each of these Entity levels will be 
	 * passed to the new entity. The universal 'user_id' link field will 
	 * be set and 'id' will be unset if empty (otherwise the id value 
	 * generated on insert isn't passed back in the Entiy on save). go figure.
	 * 
	 * @param array $data The raw request data
	 */
    private function setupEntities($data) {
        foreach ($data as $entity => $columns) {
            $entity = ucfirst($entity);
            $name_spaced_entity = "App\Model\Entity\\" . $entity;
			if (empty($columns['user_id'])) {
				$columns['user_id'] = $this->_table->SystemState->artistId();
			}
			$this->created[$entity] = FALSE;
			if (empty($columns['id'])) {
				$this->created[$entity] = TRUE;
				unset($columns['id']);
			}
            $this->$entity = new $name_spaced_entity($columns);
        }
    }
    
    private function updateAssociations($table) {
		
		$updateMethod = 'updateUsing' . $table->alias();
		$this->$updateMethod($table);
        osd($table); //die;
    }
	
	/**
	 * Set up Entities associated to the Artwork Entity
	 * 
	 * @param Table $table
	 */
	private function updateUsingArtworks($table) {
		if (empty($this->Edition->artwork_id)) {
			$this->Edition->artwork_id = $this->Artwork->id;
		}		
	}
	
	/**
	 * Set up Entities associated to the Edition Entity
	 * 
	 * Format link is simple. Piece management and linking 
	 * will take some thought. Stub in place.
	 * 
	 * @param Table $table
	 */
	private function updateUsingEditions($table) {
		$this->Edition->id = '12';
		osd($this->Edition);
		if (empty($this->Format->edition_id)) {
			$this->Format->edition_id = $this->Edition->id;
		} 
		
		if ($this->created['Edition']) {
			
			// Open Editions and Limited Editions act differently
			// Unique don't need an array either
			
			// if Edition->quantity has changed, logic will have to determine 
			// how to handle the things. Decreases are the big problem. 
			
			// this is the place to make new Pieces (I'm pretty sure)
			$piece = new Piece();
			$piece->edition_id = $this->Edition->id;
			$this->Piece = array_fill(0, (integer) '10' , clone $piece);
		}
		osd($this->Piece);
	}
	
	private function updateUsingFormats($table) {
		
	}
	
	private function updateUsingPieces($table) {
		
	}
	
}

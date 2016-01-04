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
	
	protected $SystemState;
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
    
	/**
	 * Save based on stadardized TRD stack
	 * 
	 * The data array is in the old CakePHP 2.x style 
	 * <pre>
	 * [
	 *	'Artwork' => [column data],
	 *	'Edition' => [column data],
	 *	...
	 * ]
	 * </pre>
	 * All the Entities are created from array data and other logic that 
	 * controls Entity relationships. Then working from the list of Entities 
	 * named in this->stack_members, the Entities are saved one at a time. 
	 * After successful save of each Entity, downstream Entities are updated 
	 * using new data in the just save object. Mostly, this is a matter of 
	 * passing association keys down to link things together.
	 * 
	 * @param array $data this->request-data from the form
	 * @return boolean Success or failure of the save process
	 */
    public function saveStack($data) {
		osd($data);die;
		// insure null IDs
		// adjust image nodes
		// save the stack
		// analize for Piece requirements
		// save pieces
// <editor-fold defaultstate="collapsed" desc="old code">
//		unset($data['id']);
//		unset($data['editions'][0]['id']);
//		unset($data['editions'][0]['formats'][0]['id']);
//		$entity = new Artwork($data);
//		$ed = new Edition($data['editions'][0]);
//		$fo = new Format($data['editions'][0]['formats'][0]);
//		$entity['editions'][0] = $ed;
//		$entity['editions'][0]['formats'][0] = $fo;
//		osd($entity);
//		$Artworks = \Cake\ORM\TableRegistry::get('Artworks');
//		$Editions = \Cake\ORM\TableRegistry::get('Editions');
//		$Formats = \Cake\ORM\TableRegistry::get('Formats');
//		$Artworks->save($entity);
//		osd($entity);
//		die;
////		$this->success = TRUE;
//$this->setupEntities($data);
//		// start transaction
//foreach ($this->stack_members as $entity) {
//	$alias = Inflector::pluralize($entity);
//	$table = \Cake\ORM\TableRegistry::get($alias);
//
//
//	if (is_array($this->$entity)) {
//				foreach ($this->$entity as $entity) {
//					if (!$table->save($entity)) {
//						// rollback transaction
//						return FALSE;
//					}
//				}
//			} else {
//				if ($table->save($this->$entity)) {
////				if (true) {
//					$this->updateAssociations($table);
//				} else {
//					// rollback transaction
//			return false;
//				}
//			}
//		}
//
//		return true; 
// </editor-fold>

    }
	
	/**
	 * Generate the Entities represented in the data
	 * 
	 * $data must have top level indexes that match the names of Entity 
	 * classes. We'll walk through those and do some preliminary adjustments 
	 * to the data for each Entity. the universal 'user_id' link field will 
	 * be set and 'id' will be unset if empty (otherwise the id value generated 
	 * on insert isn't passed back in the Entiy on save). go figure. 
	 * Once the data is ready, an Entity will be created from it.
	 * Some Entities take control of the creation of others, so those cases 
	 * are detected and handled.
	 * 
	 * @param array $data The raw request data
	 */
    private function setupEntities($data) {
        foreach ($data as $entity => $columns) {
			// if not created by earlier process
			if (!$this->$entity) {
				if (empty($columns['user_id'])) {
					$columns['user_id'] = $this->_table->SystemState->artistId();
				}

				if (empty($columns['id'])) {
					unset($columns['id']);
				}

				$entity_class = "App\Model\Entity\\" . $entity;
				$this->$entity = new $entity_class($columns);
				
				$this->mediatedEntitySetup($entity);
			}			
        }
    }
	
	/**
	 * Factory to select an Entity Setup process
	 * 
	 * Some Entities are not simply created from TRD. Instead their creation 
	 * is controlled by information or choices in other Entities. The main 
	 * setup method always calls here to allow these special processes. 
	 * 
	 * @param string $entity_name
	 */
	private function mediatedEntitySetup($entity_name) {
		if (in_array($entity_name, ['Series', 'Subscription', 'Edition'])) {
			$setupMethod = 'setupUsing' . $entity_name;
			$this->$setupMethod($entity_name);
		}
	}
    
	/**
	 * Factory to select an Entity Update process
	 * 
	 * Once a record is saved, it may aquire data (like an id) that is needed 
	 * to link other pending records to it. This factory will select the 
	 * appropriate post-save process to link up the layers of the Artwork stack. 
	 * 
	 * @param Table $table
	 */
    private function updateAssociations($table) {
		$updateMethod = 'updateUsing' . $table->alias();
		$this->$updateMethod($table);
//        osd($table); //die;
    }
	
	private function setupUsingSeries($entity_name) {
		
	}
	
	private function setupUsingSubscription($entity_name) {
		
	}
	
	private function setupUsingEdition($entity_name) {
		$Entity = $this->$entity_name;
		$artist_id = $this->_table->SystemState->artistId();
		if (empty($Entity->id)) {
			switch ($Entity->type) {
				case 'Limited Edition':
				case 'Portfolio':
					$i = 0;
					while ($i++ < $this->Edition->quantity) {
						$this->Piece[$i-1] = new Piece(['number' => $i, 'user_id' => $artist_id]);
					}
					break;
				case 'Open Edition':
				case 'Unique':
				case 'Use':
					$this->Piece = new Piece(['user_id' => $artist_id]);
					break;
			}
		} elseif ($Entity->dirty('quantity')) {
			// this would handle change of quantity for an existing Edition
		}
		
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
		if (empty($this->Format->edition_id)) {
			$this->Format->edition_id = $this->Edition->id;
		} 
		if (is_array($this->Piece)) {
			$i = 0;
			$count = count($this->Piece);
			while ($i++ < $count) {
				$this->Piece[$i-1]->edition_id = $this->Edition->id;
			}
		} else {
			if (empty($this->Piece->edition_id)) {
				$this->Piece->edition_id = $this->Edition->id;
			} 
		}
	}
	
	private function updateUsingFormats($table) {
		
	}
	
	private function updateUsingPieces($table) {
		
	}
	
}

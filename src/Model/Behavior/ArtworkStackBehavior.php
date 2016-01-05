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
use Cake\Collection\Collection;

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
	 * named in this->stack_members...
	 * 
	 * @param array $data this->request-data from the form
	 * @return boolean Success or failure of the save process
	 */
    public function saveStack($data) {
		osd($data);
		// insure null IDs
		// insure user_id in new entities
		if ($this->_table->SystemState->is(ARTWORK_CREATE)) {
			$data = $this->initIDs($data);
		}
		// adjust image nodes
		$data = $this->initImages($data);
		osd($data, 'after id initialization');
		// analize for Piece requirements
		// save the stack
		die;
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
	 * Set new record IDs to NULL and set artist ownership for new records
	 * 
	 * All levels except Artwork might be created in multiples at some future 
	 * point, so to keep the map operating identically at all levels, the 
	 * array has an artifical zero-th level added before being passed to 
	 * the recursive map. On return, the level is removed.
	 * 
	 * @param array $data
	 * @return array
	 */
	private function initIDs($data) {
		$artwork = new Collection([$data]);
		$modified = $artwork->map([$this, 'mapIDs']);
		return $modified->toArray()[0];
	}
	
	/**
	 * Map proper IDs to new records so they create properly
	 * 
	 * If a node's id is empty, it is being created. It won't create properly 
	 * unless it's set to NULL. These new records also need the current 
	 * artist_id set (all tables use this value). This data-point is never 
	 * set in the forms so it always needs to be added. Records that have an 
	 * ID are considered pre-existing and are passed through untouched. 
	 * This interation doesn't go key-by-key, it goes model-layer by 
	 * model-layer
	 * 
	 * @param array $record
	 * @return array
	 */
	public function mapIDs($record) {
		// if we're on the top 'artwork' level, recurse into editions level
		if (isset($record['editions'])) {
			$record['editions'] = (new Collection($record['editions']))
					->map([$this, 'mapIDs'])->toArray();
		}
		// if we're on an edition level, recurse into formats level
		if (isset($record['formats'])) {
			$record['formats'] = (new Collection($record['formats']))
					->map([$this, 'mapIDs'])->toArray();
		}
		// only change id data for brand new records
		if ($record['id'] === '') {
			$record['id'] = NULL;
			$record['user_id'] = $this->_table->SystemState->artistId();
		} 
		return $record;
	}

	private function initImages($data) {
		$artwork = $this->evaluateImage($data);
		foreach($artwork['editions'] as $index => $edition) {
			$formats = new Collection($edition['formats']);
			$artwork['editions'][$index]['formats'] = $formats->map([$this, 'evaluateImage'])->toArray();
		}
		return $artwork;
	}
	
	public function evaluateImage($record) {
		if (!isset($record['image'])) {
			return $record;
		}
		// if an image is being uploaded, prepare the environment
		if ($record['image']['image']['name'] !== '') {
			if ($record['image']['image']['error'] === 0) {
				// I'm not sure if this is the right thing to do for the upload plugin
				$record['image_id'] = $record['image']['id'] = NULL;
			} else {
				// there was an upload error. what should we do?
			}
		} else {
			// There was no upload request. Dump the image node
			unset($record['image']);
		}
		return $record;
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

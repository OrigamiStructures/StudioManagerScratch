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
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use ArrayObject;

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
	
	protected $_images_to_delete;

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
	
	public function addToSeries($series_id = NULL, $artwork_id = NULL) {
		// get the series configuration
		// insure it doesn't exist on the Artwork
		// build the components on the Artwork
		// save
	}
	
	/**
	 * The main save process for an Artwork stack
	 * 
	 * Artwork/Edition/Format/Piece is the roughly the structure this works on. 
	 * Create that stems from a Series or that involves Subscriptions are 
	 * probably going to be handled separatly and from different views.
	 * 
	 * Save artwork based on a stadard TRD stack
	 * 
	 * The data array is in CakePHP 3.x style 
	 * <pre>
	 * // artwork is the root
	 * [
	 *	'id' => value,
	 *  'artwork_column' => value,
	 *  'editions' => [
	 *		0 => [
	 *			'id' => value,
	 *			'edition_column' => value,
	 *			'formats' => [
	 *				0 => [
	 *					'id' => value,
	 *					'format_column' => value,
	 *					'image' => [
	 *						'id' => value,
	 *						'image_column' = value,
	 *					]
	 *				]
	 *			]
	 *		],
	 *	'image' => [
	 *		'id' => value,
	 *		'image_column' = value,
	 *	]
	 * ]
	 * </pre>
	 * 1   - make proper IDs for creation
	 * 1.1 - set user_id in all cases
	 * 2   - resolve ambiguity about new/old/no image
	 * 3   - perform proper Piece creation/adjustment
	 * 4   - save the assembled data in a transaction event
	 * 
	 * @param array $data this->request-data from the form
	 * @return boolean Success or failure of the save process
	 */
//    public function saveStack($data) {
//		// will this work for REVIEW?
//		// it was originally only for CREATE but thats been changed
//		// and SAVE was used to turn on necessary associations in the Tables 
//		// that or unnecessary overhead in REVIEW mode
////		if ($this->_table->SystemState->is(ARTWORK_SAVE)) {
////		}
//		$entity = $this->initPieces($entity);
//		$entity = $this->initImages($entity);
//		// analize for Piece requirements
//		$Artwork = TableRegistry::get('Artworks');
//		// save the stack
//		osd($entity);
//		return $Artwork->save($entity);
//    }
//	
	
	/**
	 * Adjust the Pieces in TRD to match the user's request
	 * 
	 * Create and Refine processes have radically different rules for 
	 * treatment of Pieces. As do the various Edition types. Here we target 
	 * the handler for each Edition in the Artwork stack and call that 
	 * handler to set up the save data for the Pieces for the Edition.
	 * 
	 * WHAT ABOUT PIECES OF FORMATS?
	 * 
	 * @param array $data
	 * @return array
	 */
	public function initPieces($data) {
		$editions = new Collection($data['editions']);
		if ($this->_table->SystemState->is(ARTWORK_CREATE)) {
			$piece_strategy = 'createPieces';
		} elseif ($this->_table->SystemState->is(ARTWORK_REFINE)) {
			$piece_strategy = 'refinePieces';
		}
		$editions_with_pieces = $editions->map([$this, $piece_strategy]);
		$data['editions'] = $editions_with_pieces->toArray();
		return $data;
	}
	
	/**
	 * Logic for creation of pieces for new Editions
	 * 
	 * Direct creation of refinment of a Format for an existing Edition 
	 * does not require Piece creation. Those calls are bounced. Later 
	 * a better, more comprehensive Piece handling plan will be required.
	 * 
	 * @param array $edition
	 * @return array
	 */
	public function createPieces($edition) {
		if ($this->_table->SystemState->controller() !== 'formats') {
			$this->Pieces = TableRegistry::get('Pieces');
			$this->Pieces->SystemState = $this->_table->SystemState;
			switch ($edition['type']) {
				case 'Limited Edition':
				case 'Portfolio':
				case 'Unique':
					$edition['pieces'] = $this->Pieces->spawn(NUMBERED_PIECES, $edition['quantity']);
					break;
				case 'Open Edition':
					$edition['pieces'] = $this->Pieces->spawn(OPEN_PIECES, 1, ['quantity' => $edition['quantity']]);
					break;
				case 'Use':
					$edition['quantity'] = 1;
					$edition['pieces'] = $this->Pieces->spawn(OPEN_PIECES, 1);
					break;
			}
		}
		return $edition;
	}
	
	public function afterSave(Event $event, ArrayObject $data, ArrayObject $options) {
		osd(func_get_args());
	}

		/**
	 * Logic for allowed editing of Pieces for Editions
	 * 
	 * @param array $edition
	 * @return array
	 */
	public function refinePieces($edition) {
		return $edition;
	}


	/**
	 * Adjust the Image nodes on TRD to match the user's intention
	 * 
	 * beforeMarshal, the Image nodes need to be normalized for proper save. 
	 * This isolates the nodes from Artworks stack that have an Image node and 
	 * passes them into the method that resolve the data. 
	 * 
	 * @param array $data
	 * @return array
	 */
	public function initImages($data) {
		$this->_images_to_delete = [];
		$artwork = $this->evaluateImage($data);
		foreach($artwork['editions'] as $index => $edition) {
			$formats = new Collection($edition['formats']);
			$artwork['editions'][$index]['formats'] = $formats->map([$this, 'evaluateImage'])->toArray();
		}
		return $artwork;
	}
	
	/**
	 * Resolve the TRD Image data for proper save 
	 * 
	 * @param array $record
	 * @return array
	 */
	public function evaluateImage($record) {
		if (!isset($record['image'])) {
			return $record;
		}
		// if an image is being uploaded, prepare the environment
		if ($record['image']['image_file']['name'] !== '') {
			if ($record['image']['image_file']['error'] === 0) {
				// I'm not sure if this is the right thing to do for the upload plugin
				$this->_images_to_delete[] = $record['image_id'];
//				unset($record['image_id']);
//				unset($record['image_id']);
//				$record->_image_id = $record->image->id = NULL;
			} else {
				// there was an upload error. what should we do?
			}
		} else {
			// There was no upload request. Dump the image node
			unset($record['image']);
			unset($record['image_id']);
		}
		return $record;
	}
		
	/**
	 * Set artist ownership for new records
	 * 
	 * All levels except Artwork might be created in multiples at some future 
	 * point, so to keep the map operating identically at all levels, the 
	 * array has an artifical zero-th level added before being passed to 
	 * the recursive map. On return, the level is removed.
	 * 
	 * @param array $data
	 * @return array
	 */
	public function initIDs($data) {
		$artwork = new Collection([$data]);
		$modified = $artwork->map([$this, 'mapIDs']);
		return $modified->toArray()[0];
	}
	
	/**
	 * Map a user_id to new records so they link properly
	 * 
	 * If a node's id is empty, it is being created. These new records need 
	 * the current artist_id set (all tables use this value). This data-point 
	 * is never set in the forms so it always needs to be added. Records that 
	 * have an ID are considered pre-existing and are passed through 
	 * untouched. This interation doesn't go key-by-key, it goes 
	 * model-layer by model-layer 
	 * 
	 * @param array $record
	 * @return array
	 */
	public function mapIDs($record) {
		
		// if we're on the top 'artwork' level, recurse into editions level
		if (isset($record['editions'])) {
			$record['editions'] = (new Collection($record['editions']))
					->map([$this, 'mapIDs'])->toArray();
			
		} elseif (isset($record['formats'])) {
		// if we're on an edition level, recurse into formats level
			$record['formats'] = (new Collection($record['formats']))
					->map([$this, 'mapIDs'])->toArray();
		
		} elseif (isset($record['image'])) {
		// if we're on an edition level, recurse into formats level
			$record['image'] = (new Collection($record['image']))
					->map([$this, 'mapIDs'])->toArray();
		}
		
		// only change id data for brand new records
		if ($record['id'] === '') {
			$record['user_id'] = $this->_table->SystemState->artistId();
		}
		return $record;
	}
	
	private function setIDs($record) {
		return $record;
	}
	
}

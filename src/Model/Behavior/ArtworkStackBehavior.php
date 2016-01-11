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
    public function saveStack($data) {
		// will this work for REVIEW?
		// it was originally only for CREATE but thats been changed
		// and SAVE was used to turn on necessary associations in the Tables 
		// that or unnecessary overhead in REVIEW mode
//		if ($this->_table->SystemState->is(ARTWORK_SAVE)) {
//		}
		$entity = $this->initPieces($entity);
		$entity = $this->initImages($entity);
		// analize for Piece requirements
		$Artwork = TableRegistry::get('Artworks');
		// save the stack
		osd($entity);
		return $Artwork->save($entity);
    }
	
	protected function initPieces($data) {
		$editions = new Collection($data['editions']);
		if ($this->_table->SystemState->is(ARTWORK_CREATE)) {
			$this->_piece_strategy = 'create';
		} elseif ($this->_table->SystemState->is(ARTWORK_REFINE)) {
			$this->_piece_strategy = 'refine';
		}
		$editions_with_pieces = $editions->map(function($edition){
			$result = $this->createPieces($edition);
			return $result;
		});
		$data['editions'] = $editions_with_pieces->toArray();
		return $data;
	}
	
	protected function createPieces($edition) {
		$this->Pieces = TableRegistry::get('Pieces');
		$this->Pieces->SystemState = $this->_table->SystemState;
		switch ($edition->type) {
			case 'Limited Edition':
			case 'Portfolio':
			case 'Unique':
				$edition->pieces = $this->Pieces->spawn(NUMBERED_PIECES, $edition->quantity);
				break;
			case 'Open Edition':
				$edition->pieces = $this->Pieces->spawn(OPEN_PIECES, 1, ['quantity' => $edition->quantity]);
				break;
			case 'Use':
				$edition->pieces = $this->Pieces->spawn(OPEN_PIECES, 1);
				break;
		}
		return $edition;
	}
	
	public function initImages($data) {
		$this->_images_to_delete = [];
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
		if ($record['image']['image_file']['name'] !== '') {
			if ($record['image']['image_file']['error'] === 0) {
				// I'm not sure if this is the right thing to do for the upload plugin
				$this->_images_to_delete[] = $record['image_id'];
				unset($record['image_id']);
				unset($record['image_id']);
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
	
}

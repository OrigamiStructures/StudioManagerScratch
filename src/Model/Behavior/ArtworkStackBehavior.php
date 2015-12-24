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
    
    public $stack_members = [
        'Artwork' => ['Edition'],
		'Series' => [],
        'Edition' => ['Format', 'Piece'],
        'Format' => ['Piece'],
        'Piece' => []
    ];
    
    public function __construct(\Cake\ORM\Table $table, array $config = array()) {
        parent::__construct($table, $config);
    }
    
    public function saveStack($data) {
		$this->success = TRUE;
        $this->setupEntities($data);
		// start transaction
        foreach ($this->stack_members as $entity => $dependencies) {
            $alias = Inflector::pluralize($entity);
            $table = \Cake\ORM\TableRegistry::get($alias);
			
			if ($this->success && $table->save($this->$entity)) {
				$this->updateDependent($entity, $table);
			} else {
				// rollback transaction
				// return false
			}
        }
		return true;
    }
    
    private function setupEntities($data) {
        foreach ($data as $entity => $columns) {
            $entity = ucfirst($entity);
            $name_spaced_entity = "App\Model\Entity\\" . $entity;
            $this->$entity = new $name_spaced_entity($columns);
        }
    }
    
    private function updateDependent($entity) {
        
    }
}

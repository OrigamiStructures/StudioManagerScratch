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
    
    public $operation_order = [
        'Artwork' => ['Edition'],
        'Edition' => ['Format', 'Piece'],
        'Format' => ['Piece'],
        'Piece' => []
    ];
    
    public function __construct(\Cake\ORM\Table $table, array $config = array()) {
        parent::__construct($table, $config);
    }
    
    public function saveStack($data) {
        $this->setupEntities($data);
        foreach ($this->operation_order as $entity) {
            $model = Inflector::pluralize($entity);
            $alias = \Cake\ORM\TableRegistry::get($model);
            $this->$alias->save($this->$entity);
            $this->amendEntity($entity, $alias);
        }
    }
    
    private function setupEntities($data) {
        foreach ($data as $entity => $columns) {
            $entity = ucfirst($entity);
            $name_spaced_entity = "App\Model\Entity\\" . $entity;
            $this->$entity = new $name_spaced_entity($columns);
        }
    }
    
    private function amendEntity($entity) {
        
    }
}

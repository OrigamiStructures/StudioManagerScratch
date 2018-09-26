<?php
namespace App\Model\Lib;

use Cake\ORM\Entity;
use App\Lib\SystemState;
use Cake\Collection\Collection;
use Cake\Utility\Inflector;

/**
 * 
 */
class IdentitySet {
	
	protected $_class_name;
	
	protected $_id;

	protected $_count;
	
	protected $_pointer_name;

	protected $_id_list;
	
	public function __construct(Entity $entity, $property_name) {
		$this->_class_name = SystemState::stripNamespace($entity);
		$this->_id = $entity->id;
		$this->_count = count($entity->$property_name);
		$this->_pointer_name = $property_name;
		$idList = new Collection($entity->$property_name);
		$this->_id_list = $idList->map(function($value, $index) {
			return $value->id;
		})->toArray();
	}
	
	public function pointsTo($inflection = NULL) {
		if (!is_null($inflection) && method_exists('Cake\Utility\Inflector', $inflection)){
			return Inflector::$inflection($this->_pointer_name);
		}
		return $this->_pointer_name;
	}
	
	public function sourceName() {
		if (!is_null($inflection) && method_exists('Cake\Utility\Inflector', $inflection)){
			return Inflector::$inflection($this->_class_name);
		}
		return $this->_class_name;
	}
	
	public function sourceId() {
		return $this->_id;
	}
	
	public function count() {
		return $this->_count;
	}
	
	public function countString() {
		return $this->_count . ' ' . 
				(($this->_count === 1) ? 
				$this->pointsTo('singularize') : $this->pointsTo('pluralize'));
	}
	
	public function idSet() {
		return $this->_id_list;
	}
}


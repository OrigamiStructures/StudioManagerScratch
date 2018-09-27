<?php
namespace App\Model\Lib;

use App\Model\Lib\IdentitySet;
use Cake\Core\ConventionsTrait;
use Cake\Utility\Inflector;
use Cake\ORM\Entity;
use App\Lib\SystemState;
use Cake\Collection\Collection;

class IdentitySets {
	
	use ConventionsTrait;
		
	protected $_sets;
	
	protected $_table_name;
	
	protected $source_table_name;

	public function __construct($TableName, $config = NULL) {
		$this->_table_name = $TableName;
		if (!is_null($config)) {
			$this->add($config);
		}
	}
	
	public function add($config) {
		$config = !is_array($config) ? [$config] : $config;
		foreach ($config as $entity) {
			if ($this->verifyClass($entity)) {
				$identitySet = new IdentitySet($entity, strtolower($this->_table_name));
				$this->_sets[$identitySet->sourceId()] = $identitySet;
			}
		}
	}
	
	private function verifyClass($object) {
		if (isset($this->source_table_name)) {
			$entity = Inflector::singularize($this->source_table_name);
			return get_class($object) === "App\Model\Entity\\$entity" ;			
		} elseif (is_subclass_of($object, 'Cake\ORM\Entity')) {
			$this->source_table_name = 
				$this->_entityName(SystemState::stripNamespace($object));
			return TRUE;
		} else {
			// Exception, didn't find expected source entity class
		}
	}
	
	public function count() {
		$sets = new Collection($this->_sets);
		return $sets->reduce(function($acc, $set) {
			return $acc + $set->count();
		}, 0);
	}
	
	public function merge() {
		$sets = new Collection($this->_sets);
		return $sets->reduce(function($acc, $set) {
			return array_merge($acc, $set->idSet());
		}, []);
	}
}
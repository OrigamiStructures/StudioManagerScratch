<?php
namespace App\Model\Lib;

use App\Model\Lib\IdentitySet;
use Cake\Core\ConventionsTrait;
use Cake\Utility\Inflector;
use Cake\ORM\Entity;
use App\Lib\SystemState;
use Cake\Collection\Collection;
use Cake\ORM\TableRegistry;
use App\Exception\BadClassConfigurationException;
use Cake\ORM\Query;
/**
 * IdentitySets
 * 
 * Contains and reports on a many IdentitySet objects. All the contained 
 * objects are based on the same source Entity type and collect the 
 * same associated Entity IDs
 * 
 * @todo This class could, if appropriate, compose itself into the Table 
 *		object of the association. This would potentially solve transport 
 *		and availability problems. Otherwise, who keeps this object?
 */
class IdentitySets {
	
	use ConventionsTrait;
		
	protected $_sets;
	
	protected $_table_name;
	
	protected $source_table_name;

	/**
	 * @todo needs to tollerate empty configs too. [], records found but not null 
	 * 
	 * @param string $TableName The related IDs to compile from the $config
	 * @param array|Entity $config A single or an array of Entities
	 */
	public function __construct($TableName, $config = NULL) {
		$this->_table_name = $TableName;
		if (!is_null($config)) {
			$this->add($config);
		}
	}
	
	/**
	 * Add one or many enties to the set of sets
	 * 
	 * @param array|Entity $entities
	 */
	public function add($entities) {
		$entities = !is_array($entities) ? [$entities] : $entities;
		foreach ($entities as $entity) {
			if ($this->verifyClass($entity)) {
				$identitySet = new IdentitySet($entity, $this->_table_name);
				$this->_sets[$identitySet->sourceId()] = $identitySet;
			}
		}
	}
	
	/**
	 * Establish the kind of entity and ensure only that kind is accepted
	 * 
	 * @param Entity $object
	 * @return boolean|string
	 */
	private function verifyClass($object) {
		if (isset($this->source_table_name)) {
			$entity = Inflector::singularize($this->source_table_name);
			return get_class($object) === "App\Model\Entity\\$entity" ;			
		} elseif (is_subclass_of($object, 'Cake\ORM\Entity')) {
			$this->source_table_name = 
				$this->_entityName(SystemState::stripNamespace($object));
			return TRUE;
		} else {
			$msg = "The IdentitySets class only accepts Entity objects";
			throw new BadClassConfigurationException($msg);
		}
	}
	
	/**
	 * How many unique IDs are stored?
	 * 
	 * @return int
	 */
	public function count() {
		return count($this->merge());
	}
	
	/**
	 * Return all the stored IDs with no duplicates
	 * 
	 * @return array
	 */
	public function merge() {
		$sets = new Collection($this->_sets);
		$merged = $sets->reduce(function($acc, $set) {
			return array_merge($acc, $set->idSet());
		}, []);
		return array_flip(array_flip($merged));
	}
	
	/**
	 * Is the id a member of any set
	 * 
	 * @param string $id
	 * @return boolean 
	 */
	public function has($id) {
		$sets = new Collection($this->_sets);
		$sets->reduce(function($acc, $set) use ($id){
			return $acc && $set->has($id);
		}, TRUE);	}
	
	/**
	 * Return any source-record IDs that relate to the set-member id
	 * 
	 * @param string $id
	 * @return array|boolean
	 */
	public function sourceFor($id) {
		$sets = new Collection($this->_sets);
		$sources = $sets->reduce(function($acc, $set) use ($id){
			$source = $set->sourceFor($id);
			if ($source) { $acc[] = $source; }
		}, []);
		return !empty($sources) ? $sources : FALSE;
	}
	
	/**
	 * Get a speicifc set object by its source id
	 * 
	 * @param string $source_id
	 * @return boolean|IdentitySet
	 */
	public function getSet($source_id) {
		if (isset($this->_sets[$source_id])) {
			return $this->_sets[$source_id];
		} else {
			return FALSE; 
		}
	}
	
	/**
	 * Get the table name for the members in the sets
	 * 
	 * @return string
	 */
	public function table() {
		return $this->_table_name;
	}
	
	/**
	 * Get the table name for the source entities
	 * 
	 * @return string
	 */
	public function sourceTable() {
		return $this->source_table_name;
	}
	
	/**
	 * Start and return a new query for them members in these sets
	 * 
	 * @return Query
	 */
	public function query() {
		$table = TableRegistry::get($this->table());
		return $table->find('all', ['conditions' => [
			'id IN ' => $this->merge(),
		]]);
	}
	
	/**
	 * Return an array of entities for all members in these sets
	 * 
	 * @return array
	 */
	public function arrayResult() {
		$table = TableRegistry::get($this->table());
		return $table->find('all', ['conditions' => [
			'id IN ' => $this->merge(),
		]])->toArray();
	}
	
}
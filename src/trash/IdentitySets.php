<?php
namespace App\Model\Lib;

use App\Model\Lib\IdentitySetBase;
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
 * same associated entity IDs
 * 
 * @todo This class could, if appropriate, compose itself into the Table 
 *		object of the association. This would potentially solve transport 
 *		and availability problems. Otherwise, who keeps this object?
 * 
 * @todo If it becomes useful to know the Source and Association names, 
 *		properties and methods to report on those exist in IdentitySet and 
 *		could be moved to the base class to make them available here.
 */
class IdentitySets extends IdentitySetBase {
	
	/**
	 * IdentitySet objects indexed by source id
	 * 
	 * Each object names IDs of one type of record that are linked to 
	 * the source record.
	 *
	 * @var array
	 */
	protected $_sets;

	/**
	 * @todo needs to tollerate empty configs too. [], zero records found 
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
		if (isset($this->source_entity_name)) {
			$entity = Inflector::singularize($this->source_entity_name);
			return get_class($object) === "App\Model\Entity\\$entity" ;			
		} elseif (is_subclass_of($object, 'Cake\ORM\Entity')) {
			$this->source_entity_name = 
				$this->_entityName(SystemState::stripNamespace($object));
			return TRUE;
		} else {
			$msg = "The IdentitySets class only accepts Entity objects";
			throw new BadClassConfigurationException($msg);
		}
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
	
// <editor-fold defaultstate="collapsed" desc="Abstract implementations">
	/**
	 * How many unique IDs are stored?
	 * 
	 * @return int
	 */
	public function count() {
		return count($this->idList());
	}


	/**
	 * Is the id a member of any set
	 * 
	 * @todo shouldn't this be $acc || $set and FALSE to start?
	 * @param string $id
	 * @return boolean 
	 */
	public function has($id) {
		$sets = new Collection($this->_sets);
		$sets->reduce(function($acc, $set) use ($id) {
			return $acc && $set->has($id);
		}, TRUE);
	}


	/**
	 * Return any source-record IDs that relate to the set-member id
	 * 
	 * @param string $id
	 * @return array|boolean
	 */
	public function sourceFor($id) {
		$sets = new Collection($this->_sets);
		$sources = $sets->reduce(function($acc, $set) use ($id) {
			$source = $set->sourceFor($id);
			if ($source) {
				$acc[] = $source;
			}
			return $acc;
		}, []);
		return !empty($sources) ? $sources : FALSE;
	}
	
	/**
	 * Return all the stored IDs with no duplicates
	 * 
	 * @return array
	 */
	public function idList() {
		$sets = new Collection($this->_sets);
		$merged = $sets->reduce(function($acc, $set) {
			return array_merge($acc, $set->idList());
		}, []);
		// use array_unique()
		return array_flip(array_flip($merged));
	}

// </editor-fold>

}
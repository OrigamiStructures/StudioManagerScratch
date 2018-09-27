<?php
namespace App\Model\Lib;

use Cake\ORM\Entity;
use App\Lib\SystemState;
use Cake\Collection\Collection;
use Cake\Utility\Inflector;

/**
 * IdentitySet manages sets of record IDs, thier context and other details
 * 
 * To pursue a flat style of query as an alternative to deep containment, we'll 
 * need to collect and transport sets of IDs to use in WHERE IN (list) queries. 
 * 
 * This object self assembles from an Entity, then reports on various contexual 
 * details and, most importantly, can deliver an IN list for further queries.
 * 
 */
class IdentitySet {
	
	/**
	 * Type of originating entity
	 *
	 * @var string
	 */
	protected $_source_name;
	
	/**
	 * ID of originating entity
	 *
	 * @var string
	 */
	protected $_source_id;

	/**
	 * Count of IDs in the list
	 *
	 * @var int
	 */
	protected $_count;
	
	/**
	 * Name of the entities referenced by IDs in the list
	 *
	 * @var string
	 */
	protected $_pointer_name;

	/**
	 * The array of IDs of the related records
	 *
	 * @var array
	 */
	protected $_id_list;
	
	/**
	 * Create the object from one entity with one containment of linked records
	 * 
	 * The provided entity must have a property ($property_name) that contains 
	 * linked records. These linked entities only need the id field.
	 * 
	 * @param Entity $entity
	 * @param string $property_name
	 */
	public function __construct(Entity $entity, $property_name) {
		$this->_source_name = SystemState::stripNamespace($entity);
		$this->_source_id = $entity->id;
		$this->_count = count($entity->$property_name);
		$this->_pointer_name = $property_name;
//		osd($this);
//		osd($entity->$property_name);die;
		$idList = new Collection($entity->$property_name);
		$this->_id_list = $idList->map(function($value, $index) {
			return $value->id;
		})->toArray();
	}
	
	/**
	 * Get the name of the table/entities the ID list references
	 * 
	 * By passing the name of an Inflector method, the output can be 
	 * modified as needed.
	 * 
	 * @param mixed $inflection The name of any Inflector method
	 * @return string
	 */
	public function pointsTo($inflection = NULL) {
		if (!is_null($inflection) && method_exists('Cake\Utility\Inflector', $inflection)){
			return Inflector::$inflection($this->_pointer_name);
		}
		return $this->_pointer_name;
	}
	
	/**
	 * Get the name of the enitity that contained the linked record(s)
	 * 
	 * By passing the name of an Inflector method, the output can be 
	 * modified as needed.
	 * 
	 * @param mixed $inflection The name of any Inflector method
	 * @return string
	 */
	public function sourceName($inflection = NULL) {
		if (!is_null($inflection) && method_exists('Cake\Utility\Inflector', $inflection)){
			return Inflector::$inflection($this->_source_name);
		}
		return $this->_source_name;
	}
	
	/**
	 * Get the ID of the source record
	 * 
	 * @return string
	 */
	public function sourceId() {
		return $this->_source_id;
	}
	
	/**
	 * Get the count of IDs in the list of linked records
	 * 
	 * @return int
	 */
	public function count() {
		return $this->_count;
	}
	
	/**
	 * Get a human readable count
	 * 
	 * eg: '6 pieces', '0 members', '12 articles' 
	 * 
	 * @return string
	 */
	public function countString() {
		return $this->_count . ' ' . 
				(($this->_count === 1) ? 
				$this->pointsTo('singularize') : $this->pointsTo('pluralize'));
	}
	
	/**
	 * An array containing the IDs of the linked records
	 * 
	 * This can be used in the queries: 
	 * `$query->where(['id' => $this->idSet()]` or 
	 * `$query->where([$this->pointsTo('singularize') . '_id' => $this->idSet()]` 
	 * 
	 * @return array
	 */
	public function idSet() {
		return $this->_id_list;
	}
	
	public function describe() {
		return "Contains {$this->countString()} linked to a {$this->sourceName()} (id: {$this->sourceId()}).";
	}
	
	public function __debug() {
		return [$this->describe()];
	}
}


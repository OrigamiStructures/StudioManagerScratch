<?php
namespace App\Model\Lib;

use App\Model\Lib\IdentitySetBase;
use Cake\ORM\Entity;
use App\Lib\SystemState;
use Cake\Collection\Collection;
use Cake\Utility\Inflector;
use Cake\Core\ConventionsTrait;
use App\Exception\MissingPropertyException;
use App\Exception\BadClassConfigurationException;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;

/**
 * IdentitySet manages sets of record IDs, thier context and other details
 * 
 * To pursue a flat style of query as an alternative to deep containment, we'll 
 * need to collect and transport sets of IDs to use in WHERE IN (list) queries. 
 * 
 * This object self assembles from an Entity, then reports on various contexual 
 * details and, most importantly, can deliver an IN list (the IDs of one 
 * specific association) for further queries.
 * 
 */
class IdentitySet extends IdentitySetBase {
	
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

	protected $_property_name;

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
	 * @todo The full range of Table name configurations (and transformations) 
	 *		has not been tested
	 * 
	 * @param Entity $entity
	 * @param string $property_name
	 */
	public function __construct(Entity $entity, $table_name) {
		$this->_table_name = $table_name;
		$this->source_entity_name = SystemState::stripNamespace($entity);
		$this->_source_id = $entity->id;
		$this->_property_name = $this->linkPropertyName($entity);
		if (is_array($entity->{$this->_property_name})) {
			$idList = new Collection($entity->{$this->_property_name});
			$this->_id_list = $idList->map(function($value, $index) {
				return $value->id !== NULL ? $value->id : NULL;
			})->toArray();
		} else {
			$this->_id_list = $entity->{$this->_property_name} !== NULL ?
				[$entity->{$this->_property_name}] :
				[] ;
		}
		$this->_count = count($this->_id_list);
	}
	
	/**
	 * Get the name of the table/entities the ID list references
	 * 
	 * By passing the name of an Inflector method, the output can be 
	 * modified as needed.
	 * 
	 * @todo If this method becomes generally useful, it would be simpler 
	 *		to pass in the which object name was required (like Table, 
	 *		Entity, or Property), rather than the name of an inflection style. 
	 *		This change would eliminate ->linkPropertyName() too.
	 * 
	 * @param mixed $inflection The name of any Inflector method
	 * @return string
	 */
	public function pointsTo($inflection = NULL) {
		if (!is_null($inflection) && 
				method_exists('Cake\Utility\Inflector', $inflection)){
			return Inflector::$inflection($this->_table_name);
		}
		return $this->_table_name;
	}
	
	/**
	 * Discover and return the property name of the linked ids
	 * 
	 * @todo This could be done with table and association classes 
	 *			but I don't have them available here and don't know how anyway. 
	 *			But without looking it up, we rely on conventions which the 
	 *			developers may have overridden 
	 * @todo See todo on ->pointsTo()
	 * 
	 * @return string
	 */
	public function linkPropertyName($entity = NULL) {
		if (!isset($this->_property_name)) {
			$properties = $entity->visibleProperties();
			
			$many = Inflector::underscore($this->_table_name);
			$one = $this->_modelKey($this->_table_name);
			
			if (in_array ($many, $properties)) {
				$this->_property_name = $many;
			} elseif (in_array ($one, $properties)){
				$this->_property_name = $one;
			} else {
				throw new MissingPropertyException(get_class($entity) . 
						" does not the property `$one` or `$many`");
			}
		}
		return $this->_property_name;
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
	
// <editor-fold defaultstate="collapsed" desc="Abstract implementations">
	/**
	 * Get the count of IDs in the list of linked records
	 * 
	 * @return int
	 */
	public function count() {
		return $this->_count;
	}


	/**
	 * Is the id stored in this list
	 * 
	 * @param string $id
	 * @return boolean
	 */
	public function has($id) {
		return in_array($id, $this->idList());
	}


	/**
	 * If the id exists, report the source record id
	 * 
	 * @param string $id 
	 * @return boolean|string FALSE or the id of the source
	 */
	public function sourceFor($id) {
		return $this->has($id) ? $this->_source_id : FALSE;
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
	public function idList() {
		return $this->_id_list;
	}

// </editor-fold>
	
}


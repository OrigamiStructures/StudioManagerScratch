<?php
namespace App\Model\Lib;

use BadMethodCallException;
use Cake\Utility\Inflector;
use App\Model\Lib\ValueSource;

/**
 * LayerAccessArgs manages the arguments used by Set/Stack/Layer::load()
 * 
 * load(), and the several methods that support and extend it make use of 
 * many parameter. This class encapsulates and manages them.
 * 
 * Targeting downstream nodes
 * ------------------------------------------
 * layer : The classes upstream from Layers will often need to name the 
 *		layer that will be operated on. 
 * lookup_index : To support record linking, `layer` content and `StackSet` 
 *		content are indexed by their ID (or primary entity ID) 
 * 
 * property : At the end of a structure-traversal, a property can be 
 *		identified as the datum of interest
 * method : At the end of a structure-traversal, a method can be 
 *		identified as the datum of interest
 * 
 * Pagination
 * All results will be paginated using these values if set
 * ------------------------------------------
 * page : which page of found data to return
 * limit : how many elements per page
 * 
 * Data filtering 
 * ------------------------------------------
 * TRUE allows the entity into the set, FALSE excludes it 
 * Easiest to build these using the filter() method
 * property || method : The source of the datum to test
 * filter_value : The value to compare
 * filter_operator : The kind of comparison to make
 * 
 * Return data structure
 * ------------------------------------------
 * Most processes return an array containing entities. The values() and 
 * keyedList() methods will reduce the result to an array of values or 
 * an indexed array respectively
 * Easiest to build 
 * value :
 * key : 
 *
 * @author dondrake
 */
class LayerAccessArgs {
	
	/**
	 * Page to return for paginated results
	 *
	 * @var int
	 */
	private $_page = FALSE;
	/**
	 * Number of entities per page
	 * 
	 * 0 = not paginated
	 * -1 = explicit 'all' request
	 * 1 = first
	 * x = number of entities per page
	 *
	 * @var int
	 */
	private $_limit = FALSE;
	/**
	 * The name of a specific property in the layer entities
	 * 
	 * Used for query in combination with (match? conditions?) 
	 * or for distinct(), keyedList() or other single value return?
	 *
	 * @var string
	 */
	private $_lookup_index = FALSE;
	/**
	 * Name of this layer property
	 *
	 * @var string
	 */
	private $_layer = FALSE;
	
	private $_property = FALSE;
	
	private $_key_source = FALSE;


	/**
	 * This could describe the comparison between property and condition
	 * 
	 * ==, !=, >, <, between, dirty, clean... there are so many options here. 
	 * How about starting with == and NOT then do more later?
	 * Or even just go property == and use this property for later expansion?
	 *
	 * @var mixed
	 */
	private $_value_source = FALSE;
	private $_filter_value = FALSE;
	private $_filter_value_isset = FALSE;
	private $_filter_operator = FALSE;	

	public function __construct() {
		return $this;
	}
	public function setLayer($param) {
		$this->_layer = $param; 
		return $this;
	}
	public function hasLayer() {
		return $this->_layer !== FALSE;
	}
	
	public function hasKeySource() {
		return $this->_key_source !== FALSE;
	}
	
	public function hasValueSource() {
		return $this->_value_source !== FALSE;
	}
	
	public function setPage($param) {
		$this->_page = $param;
		return $this;
	}
	public function paginate($page, $limit) {
		$this->setPage($page);
		$this->setLimit($limit);
	}
	/**
	 * Set the number of elements per page
	 * 
	 * -1 will return all
	 * 1 is actually 'first in page' rather than 'first in collection'
	 * 
	 * @param type $param
	 * @return \App\Model\Lib\LayerAccessArgs
	 */
	public function setLimit($param) {
		$param = $param === 'all' ? -1 : $param;
		$param = $param === 'first' ? 1 : $param;
		$this->_limit = $param;
		return $this;
	}
	/**
	 * Set the index to lookup a stored node in a layer or stack set
	 * 
	 * These nodes are stored in an array indexed by the id or the 
	 * stored entity or, in the case of a stack, the id of the 
	 * primary entity. 
	 * 
	 * @param string $param
	 * @return \App\Model\Lib\LayerAccessArgs
	 */
	public function lookupIndex($param) {
		$this->_lookup_index = $param;
		return $this;
	}
		
	private function getEntityValue($pointer, $entity) {
		if (in_array($pointer, $entity->visibleProperties())) {
			return $entity->$pointer;
		} elseif (method_exists($entity, $pointer)) {
			return $entity->$pointer();
		} else {
			return null;
		}
	}
	
	public function getKeyValue($entity) {
		$pointer = $this->_key_source;
		return $this->getValue($pointer, $entity);
	}
	
	public function getValue($entity) {
		$pointer = $this->_value_source;
		return $this->getEntityValue($pointer, $entity);
	}
	/**
	 * Set the property to be used as the value source in a filter
	 * 
	 * @param string $param
	 * @return \App\Model\Lib\LayerAccessArgs
	 */
//	public function property($param) {
//		$this->_property = $param;
//		return $this;
//	}
	/**
	 * Set a method to be used a the value source in a filter
	 * 
	 * @param string $param methodName or methodName() 
	 * @return \App\Model\Lib\LayerAccessArgs
	 */
	public function method($param) {
		$this->_method = trim($param,'() ');
		return $this;
	}
	/**
	 * Set up the filter params all at once
	 * 
	 * @param string $value_source A property_name or method_name()
	 * @param mixed $filter_value The value to compare to the $source_value
	 * @param string $filter_operator The kind of comparison to make
	 */
	public function specifyFilter($value_source, $filter_value, $filter_operator = '==') {
		$this->valueSource($value_source);
		$this->filterValue($filter_value);
		$this->setFilterOperator($filter_operator);
		return $this;
	}
	
	public function valueSource($param) {
		$this->_value_source = $param;
		return $this;
	}
	/**
	 * Set a filterValue and flag that this has been done
	 * 
	 * `filterValue` compares to the value of `valueSource` using `filterOperator`
	 * 
	 * Filtering may be done on any value, including FALSE. 
	 * So there is no safe direct test to see if a value has been stored. 
	 * Instead, filter-value-isset is marked as our indicator.
	 * 
	 * filter_operator will be assumed as == if it hasn't been set 
	 * 
	 * @param mixed $param
	 * @return \App\Model\Lib\LayerAccessArgs
	 */
	public function filterValue($param) {
		$this->_filter_value_isset = TRUE;
		$this->_filter_value = $param;
		return $this;
	}
	/**
	 * Set a comparison operation for filtering sourceValues
	 * 
	 * [==, in_array] - defaults based on filterValue type
	 * 
	 * Other options
	 * !=, ===, !==, <, >, <=, >=, 
	 * Options that won't use filterValue
	 * true (=== T), false (=== F), truthy (boolean of value) 
	 * 
	 * @param string $param
	 * @return \App\Model\Lib\LayerAccessArgs
	 */
	public function setFilterOperator($param) {
		$this->_filter_operator = $param;
		return $this;
	}
	/**
	 * Are the minimum required arguments set to allow filter operations?
	 * 
	 * Requires that 'property' or 'method' is set (xor) and 
	 * that a 'filterValue' has been set.
	 * 
	 * @return boolean
	 */
	public function isFilter() {
		return $this->valueOf('value_source') && $this->valueOf('filter_value_isset');
	}
	/**
	 * One call returns them all
	 * 
	 * Properties can be identified 
	 *		under_scored
	 *		_under_scored
	 *		underScored
	 *		UnderScored
	 * 
	 * @param string $param Name of the property to return
	 * 
	 * @return mixed
	 * @throws BadMethodCallException
	 */
	public function valueOf($param) {
		// when some_name style is submitted
		$property = '_' . trim($param, '_');
		if (isset($this->$property)){
			return $this->$property;
		}
		// when someName style is submitted
		$property = '_' . Inflector::underscore($param);
		if (isset($this->$property)){
			return $this->$property;
		}
		return '';
		if(!isset($this->$property)) {
			throw new BadMethodCallException("Request to get LayerAccessParams::$param. The property does not exist."	);
		}
		return $this->$property;
	}
}

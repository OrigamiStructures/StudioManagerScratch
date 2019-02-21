<?php
namespace App\Model\Lib;

use BadMethodCallException;
use Cake\Utility\Inflector;

/**
 * Description of LayerAccessArgs
 *
 * @author dondrake
 */
class LayerAccessArgs {
	
	/**
	 * Name of this layer property
	 *
	 * @var string
	 */
	private $_layer = FALSE;
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
	private $_property = FALSE;
	/**
	 * The name of a method of the layer entities
	 *
	 * @var string
	 */
	private $_method = FALSE;
	private $_value = FALSE;
	/**
	 * Unsure of use
	 * 
	 * This looks something like the query system. Instead I 
	 * think I go with property vs value and method vs value
	 *
	 * @var array
	 */
	private $_conditions = FALSE; // or we make 'dirty' a condition?
	/**
	 * This could describe the comparison between property and condition
	 * 
	 * ==, !=, >, <, between, dirty, clean... there are so many options here. 
	 * How about starting with == and NOT then do more later?
	 * Or even just go property == and use this property for later expansion?
	 *
	 * @var mixed
	 */
	private $_filter_value = FALSE;
	private $_filter_value_isset = FALSE;
	private $_filter_operator = FALSE;
	
	// this one is a different concept? or wouldn't need condtions perhaps
	// does this have something to do with the context when the call is made? 
	private $_source = 'entity'; //entity or original
	

	public function __construct() {
		return $this;
	}
	public function layer($param) {
		$this->_layer = $param; 
		return $this;
	}
	public function page($param) {
		$this->_page = $param;
		return $this;
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
	public function limit($param) {
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
	/**
	 * Set the property to be used as the value source in a filter
	 * 
	 * @param string $param
	 * @return \App\Model\Lib\LayerAccessArgs
	 */
	public function property($param) {
		$this->_property = $param;
		return $this;
	}
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
	 * Passing a string followed by '()' will be interpreted as the name of 
	 * a method that will return the source value for comparison. Exclude 
	 * the '()' and $source_value will be assumed to be a property
	 * 
	 * @param string $source_value A property_name or method_name()
	 * @param mixed $filter_value The value to compare to the $source_value
	 * @param string $filter_operator The kind of comparison to make
	 */
	public function filter($source_value, $filter_value, $filter_operator = '==') {
		if(preg_match('/\(\)|\( \)/', $source_value)) {
			$this->method($source_value);
		} else {
			$this->property($source_value);
		}
		$this->filterValue($filter_value);
		$this->filterOperator($filter_operator);
		return $this;
	}
	/**
	 * Set a filter value and flag that this has been done
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
	public function filterOperator($param) {
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
		return ($this->valueOf('property') xor $this->valueOf('method')) 
				&& $this->valueOf('filter_value_isset');
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
		if(!isset($this->$property)) {
			throw new BadMethodCallException("Request to get LayerAccessParams::$param. The property does not exist."	);
		}
		return $this->$property;
	}
}

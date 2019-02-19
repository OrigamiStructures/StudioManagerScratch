<?php
namespace App\Model\Lib;

use BadMethodCallException;
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
	 * used for query in combination with (match? conditions?)
	 * or for distinct(), keyedList() or other single value return?
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
	private $_comparison_value = FALSE;
	private $_comparison_value_isset = FALSE;
	private $_comparison_operator = FALSE;
	
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
	public function limit($param) {
		$param = $param === 'all' ? -1 : $param;
		$param = $param === 'first' ? 1 : $param;
		$this->_limit = $param;
		return $this;
	}
	public function lookupIndex($param) {
		$this->_lookup_index = $param;
		return $this;
	}
	public function property($param) {
		$this->_property = $param;
		return $this;
	}
	public function method($param) {
		$this->_method = $param;
		return $this;
	}
	public function conditions($param) {
		$this->_conditions = $param;
		return $this;
	}
	public function comparisonValue($param) {
		$this->_comparison_value_isset = TRUE;
		$this->_comparison_value = $param;
		return $this;
	}
	public function comparisonOperator($param) {
		$this->_comparison_operator = $param;
		return $this;
	}
	public function isFilter() {
		return ($this->valueOf('property') || $this->valueOf('method')) && $this->valueOf('comparison_value_isset');
	}
	public function valueOf($param) {
		$property = '_' . trim($param, '_');
		if(!isset($this->$property)) {
			throw new BadMethodCallException("Request to get LayerAccessParams::$param. The property does not exist.");
		}
		return $this->$property;
	}

	
}

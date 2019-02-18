<?php
namespace App\Model\Lib;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
	private $_conditions = []; // or we make 'dirty' a condition?
	/**
	 * This could describe the comparison between property and condition
	 * 
	 * ==, !=, >, <, between, dirty, clean... there are so many options here. 
	 * How about starting with == and NOT then do more later?
	 * Or even just go property == and use this property for later expansion?
	 *
	 * @var mixed
	 */
	private $_match = FALSE;
	
	// this one is a different concept? or wouldn't need condtions perhaps
	// does this have something to do with the context when the call is made? 
	private $_source = 'entity'; //entity or original
	
	private $_unlocked = TRUE;

	public function __construct() {
		return $this;
	}
	public function layer($param) {
		if ($this->_unlocked) $this->_layer = $param; 
		return $this;
	}
	public function page($param) {
		if ($this->_unlocked) $this->_page = $param;
		return $this;
	}
	public function limit($param) {
		$param = $param === 'all' ? -1 : $param;
		$param = $param === 'first' ? 1 : $param;
		if ($this->_unlocked) $this->_limit = $param;
		return $this;
	}
	public function property($param) {
		if ($this->_unlocked) $this->_property = $param;
		return $this;
	}
	public function method($param) {
		if ($this->_unlocked) $this->_method = $param;
		return $this;
	}
	public function conditions($param) {
		if ($this->_unlocked) $this->_conditions = $param;
		return $this;
	}
	public function match($param) {
		if ($this->_unlocked) $this->_match = $param;
		return $this;
	}
	
	public function valueOf($param) {
//		$this->_unlocked = FALSE;
		$property = '_' . trim($param, '_');
		if(!isset($this->$property)) {
			throw new BadMethodCallException("Request to get LayerAccessParams::$param. The property does not exist.");
		}
		return $this->$property;
	}

	
}

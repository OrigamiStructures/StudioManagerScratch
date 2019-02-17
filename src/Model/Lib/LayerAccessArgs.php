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

	private $_layer = '';
	private $_page = 1;
	private $_limit = -1;
	private $_property = '';
	private $_method = '';
	private $_conditions = []; // or we make 'dirty' a condition?
	private $_match = TRUE;
	// this one is a different concept? or wouldn't need condtions perhaps
	private $_source = 'entity'; //entity or original
	private $_params;
	
	private $_unlocked = TRUE;

	public function __construct() {
		$this->_params = array_keys(get_class_vars($this));
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
		$this->_unlocked = FALSE;
		$property = '_' . trim($param, '_');
		if(!in_array($property, $this->_params)) {
			throw new BadMethodCallException("Request to get LayerFilterParams::$param. The property does not exist.");
		}
		return $this->$property;
	}

	
}

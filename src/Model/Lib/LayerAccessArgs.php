<?php
namespace App\Model\Lib;

use BadMethodCallException;
use Cake\Utility\Inflector;
use App\Model\Lib\ValueSource;
use Cake\Error\Debugger;

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
 * id_index : To support record linking, `layer` content and `StackSet` 
 *		content are indexed by their ID (or primary entity ID) 
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
 * Easiest to build these using the specifyFilter() method
 * value_source : The source of the datum to test (property or method)
 *		methods must not require arguments
 * filter_value : The value to compare
 * filter_operator : The kind of comparison to make
 * 
 * Return data structure
 * ------------------------------------------
 * Most processes return an array containing entities. The values() and 
 * keyedList() methods will reduce the result to an array of values or 
 * an indexed array respectively
 * 
 * @author dondrake
 */
class LayerAccessArgs {
	
	protected $_errors = [];


// <editor-fold defaultstate="collapsed" desc="PAGINATION PROPERTIES">
	
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

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="ID-INDEX PROPERTY">

	/**
	 * The name of a specific property in the layer entities
	 * 
	 * Used for query in combination with (match? conditions?) 
	 * or for distinct(), keyedList() or other single value return?
	 *
	 * @var string
	 */
	private $_id_index = FALSE; 

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="LAYER PROPERTY">

	/**
	 * Name of this layer property
	 *
	 * @var string
	 */
	private $_layer = FALSE;
	
	// </editor-fold>
	
// <editor-fold defaultstate="collapsed" desc="VALUE-SOURCE PROPERTIES">

	private $_key_source = FALSE;
	private $_value_source = FALSE;
	public $KeySource;
	public $ValueSource;

	// </editor-fold>
	
// <editor-fold defaultstate="collapsed" desc="FILTER PROPERTIES">
	
	private $_filter_value = FALSE;
	private $_filter_value_isset = FALSE;
	private $_filter_operator = FALSE; 

// </editor-fold>

	public function __construct() {
		return $this;
	}

// <editor-fold defaultstate="collapsed" desc="ERROR MANAGEMENT">

	private function registerError($message) {
		$trace = collection(Debugger::trace(['start' => 2, 'format' => 'points']));
		$stack = $trace->reduce(function($accum, $node){
			$namespace = explode('/', $node['file']);
			$file = array_pop($namespace);
			$folder = array_pop($namespace);
			$namespace = implode('/', $namespace);
			$accum[] = "Line {$node['line'] } in $folder/$file:\t$namespace";
			return $accum;
		}, []);
		$error = [$message, $stack];
		$this->_errors[] = $error;
		pr($error);
	}


	public function getErrors() {
		return $this->_errors;
	}

// </editor-fold>
	
// <editor-fold defaultstate="collapsed" desc="LAYER ARGUMENT">

	public function setLayer($param) {
		if ($this->hasLayer() && $this->valueOf('layer') != $param) {
			$this->registerError('Can\'t change `layer` after it\'s been set.');
		} else {
			$this->_layer = $param;
			$this->setupValueObjects('layer');
		}
		return $this;
	}
	
	public function getSourceObject() {
		return $this->ValueSource;
	}

	public function setValueSource($param) {
		if ($this->hasValueSource() && $this->valueOf('valueSource') != $param) {
			$this->registerError('Can\'t change `valueSource` after it\'s been set.');
		} else {
			$this->_value_source = $param;
			$this->setupValueObjects('value');
		}
		return $this;
	}

	public function setKeySource($param) {
		if ($this->hasKeySourceSource() && $this->valueOf('keySource') != $param) {
			$this->registerError('Can\'t change `keySource` after it\'s been set.');
		} else {
			$this->_value_source = $param;
			$this->setupValueObjects('key');
		}
		return $this;
	}
	
	private function setupValueObjects($origin) {
		switch ($origin) {
			case 'layer':
				if (!$this->hasValueObject() && $this->hasValueSource()) {
					$this->buildValueObject();
				}
				if (!$this->hasKeyObject() && $this->hasKeySource()) {
					$this->buildKeyObject();
				}
				break;
			case 'value':
				if (!$this->hasValueObject() && $this->hasLayer()) {
					$this->buildValueObject();
				}
				break;
			case 'key':
				if (!$this->hasKeyObject() && $this->hasLayer()) {
					$this->buildKeyObject();
				}
				break;
			default:
				$message = 'setupValueObjects called with unknown origin';
				$this->registerError($message);
				break;
		}
	}
	
	private function buildKeyObject() {
		$this->KeySource = new ValueSource($this->valueOf('layer'), $this->valueOf('keySource'));
	}
	
	private function buildValueObject() {
		$this->ValueSource = new ValueSource($this->valueOf('layer'), $this->valueOf('valueSource'));
	}

// </editor-fold>
	
// <editor-fold defaultstate="collapsed" desc="VALIDATION CALLS -- hasXX(), isXX()">
	
	public function hasLayer() {
		return $this->_layer !== FALSE;
	}


	public function hasKeySource() {
		return $this->_key_source !== FALSE;
	}


	public function hasValueSource() {
		return $this->_value_source !== FALSE;
	}
	
	public function hasValueObject() {
		return isset($this->ValueSource);
	}
	
	public function hasKeyObject() {
		return isset($this->KeySource);
	}

	/**
	 * Are the minimum required arguments set to allow filter operations?
	 * 
	 * Requires 'valueSource and that a 'filterValue' has been set.
	 * 
	 * @return boolean
	 */
	public function isFilter() {
		return $this->valueOf('value_source') && $this->valueOf('filter_value_isset');
	}

// </editor-fold>
	
// <editor-fold defaultstate="collapsed" desc="PAGINATION ARGUMENTS">

	/**
	 * Set a page to get and how many units are on the page
	 * 
	 * @param int $page
	 * @param int $limit
	 */
	public function setPagination($page, $limit) {
		$this->setPage($page);
		$this->setLimit($limit);
	}

	public function setPage($param) {
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
	public function setLimit($param) {
		$param = $param === 'all' ? -1 : $param;
		$param = $param === 'first' ? 1 : $param;
		$this->_limit = $param;
		return $this;
	}

// </editor-fold>
	
// <editor-fold defaultstate="collapsed" desc="ID-INDEX ARGUMENT">
	
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
	public function setIdIndex($param) {
		$this->_id_index = $param;
		return $this;
	}

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="VALUE RETRIEVAL -- PROPOSED --">

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

// </editor-fold>
	
// <editor-fold defaultstate="collapsed" desc="FILTER ARGUMENTS">
	
	/**
	 * Set up the filter params all at once
	 * 
	 * @param string $value_source A property_name or method_name()
	 * @param mixed $filter_value The value to compare to the $source_value
	 * @param string $filter_operator The kind of comparison to make
	 */
	public function specifyFilter($value_source, $filter_value, $filter_operator = FALSE) {
		$this->setValueSource($value_source);
		$this->filterValue($filter_value);
		$this->setFilterOperator($filter_operator);
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

// </editor-fold>
	
// <editor-fold defaultstate="collapsed" desc="UNIVERSAL GETTER">
	
	/**
	 * One call returns them all
	 * 
	 * Properties can be identified 
	 * 		under_scored
	 * 		_under_scored
	 * 		underScored
	 * 		UnderScored
	 * 
	 * @param string $param Name of the property to return
	 * 
	 * @return mixed
	 * @throws BadMethodCallException
	 */
	public function valueOf($param) {
		// when some_name style is submitted
		$property = '_' . trim($param, '_');
		if (isset($this->$property)) {
			return $this->$property;
		}
		// when someName style is submitted
		$property = '_' . Inflector::underscore($param);
		if (isset($this->$property)) {
			return $this->$property;
		}
		return '';
		if (!isset($this->$property)) {
			throw new BadMethodCallException("Request to get LayerAccessParams::$param. The property does not exist.");
		}
		return $this->$property;
	}

// </editor-fold>
	
}

<?php
namespace App\Model\Lib;

use App\Exception\MissingPropertyException;
use App\Interfaces\LayerAccessInterface;
use App\Interfaces\LayerTaskInterface;
use App\Interfaces\xxxLayerAccessInterface;
use BadMethodCallException;
use Cake\Utility\Inflector;
use App\Model\Lib\ValueSource;
use App\Lib\Traits\ErrorRegistryTrait;
use App\Model\Lib\ValueSourceRegistry;

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
 * properties found in PaginationComponent
 * limit, maxLimit, sortWhiteList, finder, sort, direction, page, order
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
class LayerAccessArgs implements LayerAccessInterface {

use ErrorRegistryTrait;

    /**
     * @var LayerTaskInterface
     */
    protected $data;

protected $_registry;

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

// <editor-fold defaultstate="collapsed" desc="LAYER PROPERTY">

	/**
	 * Name of this layer property
	 *
	 * @var string
	 */
	private $_layer = FALSE;

	// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="VALUE-SOURCE PROPERTIES">

	private $source_node = [
		'value' => FALSE,
		'key' => FALSE,
		'filter' => FALSE
	];

	// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="FILTER PROPERTIES">

	private $_filter_value = FALSE;
	private $_filter_value_isset = FALSE;
	private $_filter_operator = FALSE;

// </editor-fold>

//<editor-fold desc="SORT VALUES">

    private $_sortDir = FALSE;
    private $_sortType = FALSE;
    private $_sort_value_isset = FALSE;

//</editor-fold>

    //<editor-fold desc="NEW METHODS FOR REFACTOR">
    public function resetData()
    {
        unset($this->data);
    }

    public function hasFilter()
    {
        return $this->source_node['filter'] && $this->valueOf('filter_value_isset');
    }

    public function hasSort()
    {
        return $this->_sortDir !== FALSE && $this->_sortType !== FALSE && $this->valueOf('sort_value_isset');
    }

    public function hasPagination()
    {
        return $this->_page !== FALSE && $this->_limit !== FALSE;
    }

    /**
     * Get the result as an array of entities
     *
     * @throws MissingPropertyException
     * @return array
     */
    public function toArray()
    {
        $this->_validateExecution();
        return $this->data->toArray();

    }

    /**
     * Get the result as Layer object
     *
     * @throws MissingPropertyException
     * @return Layer
     */
    public function toLayer()
    {
        $this->_validateExecution();
        return $this->data->toLayer();
    }

    /**
     * Get an array of values
     *
     * @param $valueSource string|ValueSource
     * @throws MissingPropertyException
     * @return array
     */
    public function toValueList($valueSource = null)
    {
        $this->_validateExecution();
        return $this->data->toValueList($valueSource);
    }

    /**
     * Get a key => value list
     *
     * @param $keySource string|ValueSource
     * @param $valueSource string|ValueSource
     * @throws MissingPropertyException
     * @return array
     */
    public function toKeyValueList($keySource = null, $valueSource = null)
    {
        $this->_validateExecution();
        return $this->data->toKeyValueList($valueSource, $keySource);
    }

    /**
     * Get a list of distinct values
     *
     * @param $valueSource string|ValueSource
     * @throws MissingPropertyException
     * @return array
     */
    public function toDistinctList($valueSource = null)
    {
        $this->_validateExecution();
        return $this->data->toDistinctList($valueSource);
    }

    /**
     * Get the stored registry instance
     *
     * @return ValueSourceRegistry
     */
    public function getValueRegistry()
    {
        // TODO: Implement getValueRegistry() method.
    }

    /**
     * Insure data is present and flag $this as process initiator
     *
     * @throws MissingPropertyException
     * @return void
     */
    protected function _validateExecution()
    {
        if (!isset($this->data)){
            $msg = 'Processing was requested on a bare LayerAccessArgs object.';
            throw new MissingPropertyException($msg);
        }
    }

    public function clearPendingFlag()
    {
        $this->_pending = FALSE;
    }

    //</editor-fold>

	public function __construct($data = FALSE) {
		$this->_registry = new ValueSourceRegistry();
        if($data) {
            $this->data = $data;
        }
		return $this;
	}

    public function data() {
        return $this->data;
    }

	public function registry() {
		return $this->_registry;
	}

    /**
     * Final processing tool for layer access arguments
     *
     * This method executes the chained layer access arguments, and returns
     * the resulting dataset as either an array (default) or as a layer object.
     *
     * @param bool $asArray
     * @return array|Layer
     */
	public function load($asArray = LAYERACC_ARRAY) {
		$result = $this->data()->load($this);
		if (!$asArray) {
			$result = new Layer($result, $this->valueOf('layer'));
		}
		return $result;
	}

    public function loadDistinct($sourcePoint = null) {
        return $this->data()->loadDistinct($this, $sourcePoint);
    }

	public function loadKeyValueList($key = NULL, $value = NULL) {
		if (!is_null($key)) {
			$this->setAccessNodeObject('key', $key);
		}
		if (!is_null($value)) {
			$this->setAccessNodeObject('value', $value);
		}
		return $this->data()->loadKeyValueList($this);
	}

	public function loadValueList($valueName = NULL) {
		if (!is_null($valueName)) {
			$this->setAccessNodeObject('value', $valueName);
		}
		return $this->data()->loadValueList($this);
	}

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

	public function accessNodeObject($name) {
		return $this->registry()->get($name);
	}

	public function setAccessNodeObject($objectName, $nodeName) {
		if (
				$this->hasAccessNodeName($objectName)
				&& $this->source_node[$objectName] != $nodeName)
		{
			$this->registerError("Can't change `{$objectName}` object's "
				. "source node name after it's been set.");
		} else {
			$this->source_node[$objectName] = $nodeName;
			$this->setupValueObjects($objectName);
		}
		return $this;

	}

	/**
	 * Make a ValueSource object or defer the task for later
	 *
	 * 'layer'
	 *		if the Value and Key objects haven't been made yet but
	 *		the source node is know for either, we can now make
	 *		that object since the layer is now known
	 * 'value'
	 *		set the layer if we can
	 *		if the ValueObject isn't yet constructued but the layer is
	 *		known, make the object since the source node is now known
	 * 'key'
	 *		set the layer if we can
	 *		if the KeyObject isn't yet constructed but the layer is
	 *		known, make the object since the key node is now known

	 *
	 * @param type $origin
	 */
	private function setupValueObjects($origin) {
		switch ($origin) {
			case 'layer':
				$this->registerSourceNodes();
				break;
			default:
                $this->evaluateLayer();
				if (!$this->hasAccessNodeObject($origin) && $this->hasLayer()) {
					$this->buildAccessObject($origin);
				}
				break;
		}
	}

	private function registerSourceNodes() {
		foreach (array_keys($this->source_node) as $name) {
			if (!$this->hasAccessNodeObject($name) && $this->hasAccessNodeName($name)) {
				$this->buildAccessObject($name);
			}
		}
	}

    private function evaluateLayer() {
        if (!$this->hasLayer() && is_a($this->data(), 'App\Model\Lib\Layer')) {
            $this->setLayer($this->data()->layerName());
        }
    }

	private function buildAccessObject($name) {
		$result = $this->registry()->load(
				$name,
				[
					'entity' => $this->valueOf('layer'),
					'node' => $this->source_node[$name]
				]
			);
	}

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="VALIDATION CALLS -- hasXX(), isXX()">

	public function hasLayer() {
		return $this->_layer !== FALSE;
	}


	public function hasAccessNodeName($name) {
		return $this->source_node[$name] !== FALSE;
	}

	public function hasAccessNodeObject($name) {
		return !is_null($this->registry()->get($name));
	}

	/**
	 * Are the minimum required arguments set to allow filter operations?
	 *
	 * Requires 'valueSource and that a 'filterValue' has been set.
	 *
	 * @return boolean
	 */
	public function isFilter() {
		return $this->source_node['filter'] && $this->valueOf('filter_value_isset');
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
		$this->setFilterOperator($filter_operator);
		$this->setAccessNodeObject('filter', $value_source);
		$this->filterValue($filter_value);
		return $this;
	}

    /**
     * Set the property or method that will be filtered
     *
     * @param $value_source string
     */
	public function setFilterTestSubject($value_source) {
        $this->setAccessNodeObject('filter', $value_source);
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
		if (!$this->valueOf('filterOperator')) {
			if (is_array($param)) {
				$default_operator = 'in_array';
			} else {
				$default_operator = '==';
			}
			$this->setFilterOperator($default_operator);
		}
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

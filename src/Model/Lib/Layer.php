<?php
namespace App\Model\Lib;

use Cake\Core\ConventionsTrait;
use Cake\ORM\Enitity;
use Cake\Collection\Collection;
use App\Exception\BadClassConfigurationException;
use \App\Interfaces\LayerAccessInterface;
use App\Model\Traits\LayerAccessTrait;
use App\Model\Lib\LayerAccessArgs;
use App\Lib\Traits\ErrorRegistryTrait;

/**
 * StackLayer
 * 
 * Streamline access to arrays of entities through a simple set of introspection 
 * and retrieval methods.
 * 
 * There are also some very basic filtering and sorting tools but it's not clear 
 * how much use they would be. But it's also not clear whether they would be needed. 
 * We may want to remove some of that code.
 *
 * @author Main
 */
class Layer implements LayerAccessInterface {
    
    use ConventionsTrait;
	use LayerAccessTrait;
	use ErrorRegistryTrait;
    
    /**
     * The lower case, singular name of this layer (matches the entity type)
     *
     * @var string
     */
    protected $_layer;
    
    /**
     * Name of the stored entity class
     *
     * @var string
     */
    protected $_className;

    /**
     * The stored entities in this set
     *
     * @var array
     */
    protected $_data = [];
    
    /**
     * The properties that can be found on the entities
     *
     * @var array
     */
    protected $_entityProperties = [];

    /**
     * Populate the object
     * 
     * This sets the layer name (lower class singular camelized),
     * name of the stored entity (eg: Format, Address, Piece),
     * and stores the provided entities indexed by their IDs.
     * 
     * The actual entity class name will be used to set the two text 
     * property values unless the array is empty. In that case, the name 
     * must be provided on the second param or an exception is raised. 
     * 
     * @param array $entities
     * @param string $type Forced to camalized, ignored if entities are present
     * @throws BadClassConfigurationException
     */
    public function __construct(array $entities = [], $type = NULL) {
        try {
            
            $this->_initClassProperties($entities, $type);
            $this->_initEntitySet($entities);
            
        } catch (Exception $ex) {

            throw $ex;
            
        }
    }
	
    /**
     * Does the set contain an entity with ID = $id
     * 
     * @param string $id
     * @return boolean
     */
    public function hasId($id) {
        return isset($this->_data[$id]);
    }
    
	/**
	 * The type/name of this layer data
	 * 
	 * @return string
	 */
    public function layerName() {
        return $this->_layer;
    }
    
	/**
	 * The entity class name of the stored objects
	 * 
	 * @param string $style 'bare' or 'namespaced'
	 * @return string
	 */
    public function entityClass($style = 'bare') {
		if ($style === 'bare') {
			return $this->_className;
		} else {
			return 'App\\Model\\Entity\\'.$this->_className;
		}
        
    }
    
    /**
     * Are are all the entities ( NOT dirty() )
     * 
     * @todo This may not be appropriate. Make this immutable and handle edits in 
     *      different structures external to this object? The entities can easily 
     *      be taken out and put in a different object (except they are then 
     *      references). But then again, when we emit arrays, they are references to 
     *      these contained objects and so, might change
     * 
     * @return boolean
     */
    public function isClean() {
        $set = new Collection($this->_data);
        $result = $set->reduce(function ($accumulated, $entity) {
                return $accumulated && !($entity->isDirty());
             }, TRUE);
        return $result;
    }
	
//	public function functionName($param) {
//		
//		
//		$args = (new LayerFilterParms())
//				->layer('addresses')
//				->property('state')
//				->conditions(['CA']);
//		
//		// to get the values out
//		$value = $args->valueOf('conditions');
//		
//		$result = $stacks->load($args);
//				
//		$args = [
//			'layer' => '',
//			'page' => 1,
//			'limit' => -1,
//			'property' => '',
//			'method' => '',
//			'conditions' => [],
//			'match' => TRUE,
//		];
//	}
	
    
    /**
     * The count of stored entities in this layer
     * 
     * @return int
     */
    public function count() {
        return count($this->_data);
    }
    
	/**
	 * Perform data load from Layer context
	 * 
	 * No args gets the id-indexed array of all stored entities
	 * Arg [lookup-index] gets the entity stored under that id/index value
	 *		if the index is invalid, an empty array is returned
	 * If a filter is set, the data is filtered, then paginated and returned
	 * Otherwise, the full set is paginated and returned
	 * 
	 * @param LayerAccessArgs $argObj
	 * @return array
	 */
	public function load(LayerAccessArgs $argObj = null) {
		if(is_null($argObj)) {
			return $this->_data;
		}

		if ($argObj->valueOf('idIndex')) {
			$id = $argObj->valueOf('idIndex');
            if (!$this->hasId($id)) {
                return [];
            }
            return $this->_data[$id];
		}
		
		if ($argObj->isFilter()) {
//			$result = $this->filter($argObj->valueOf('value_source'), $argObj->valueOf('filter_value'));
//			$result = $this->filter($this->vsSwap($argObj), $argObj->valueOf('filter_value'));
			$result = $this->filter($argObj);
		} else {
			$result = $this->_data;
		}

		return $this->paginate($result, $argObj);
		
	}
	
	/**
	 * Run a filter process on entities in an array
	 * 
	 * Support the Layer::load by doing original/desired filter testing
	 * This is the final stop of all three levels of load(). If some simple 
	 * case wasn't requested, the matching data will be sought here.
	 * 
	 * $value_source can be the name of a property or a method on the 
	 *		entity. Methods must require no arguemnts.
	 * $test_value is will be compared to $value_source's result
	 * $operator is the kind of comparison to be done. The actual function 
	 *		that does comparison will be looked up (selectComparison($op)) 
	 *		using $operator as a lookup key
	 * 
	 * @param string $value_source name of a property or method
	 * @param mixed $test_value 
	 * @param string $operator A comparison operator
	 * @return array
	 */
    public function xfilter($value_source, $test_value = null, $operator = null) {
		
		// SHUNT TO NEW FILTER
		if (is_a($value_source, '\App\Model\Lib\LayerAccessArgs')) {
			return $this->newFilter($value_source);
		}
			
		if	(	!$this->has($value_source) && 
				!method_exists($this->entityClass('namespaced'), $value_source)) 
		{
            return [];
        }
		if(is_null($operator)) {
			$operator = is_array($test_value) ? 'in_array' : '==';
		}

		$comparison = $this->selectComparison($operator);
		
        $set = collection($this->_data);
        $results = $set->filter(function ($entity, $key) 
				use ($value_source, $test_value, $comparison) {
				if(in_array($value_source, $entity->visibleProperties())) {
					$actual = $entity->$value_source;
				} else {
					$actual = $entity->$value_source();
				}
				return $comparison($actual, $test_value);
            })->toArray(); 
        return $results;
    }
	
	protected function normalizeArgs($params) {
		
		if (is_a($params[0], '\App\Model\Lib\LayerAccessArgs')) {
			return $params[0];
		}
		$argObj = $this->accessArgs();
		$args = func_get_args()[0];
		switch ( count($args) ) {
			case 2:
				$argObj->specifyFilter(
						$args[0],
						$args[1]);
				break;
			case 3:
				$argObj->specifyFilter(
						$args[0], //value source
						$args[1], //filter value
						$args[2]); //filter operator
			default:
//				pr(func_get_args());//die;
//				throw new \BadMethodCallException('Bad arguments for Layer::filter() provided.');
				break;
		}
		return $argObj;
	}
	
	/**
	 * Filter this layers set of entities
	 * 
	 * Supply an LayerAccessArg object with a `specifyFilter()` done or provide 
	 * `value-source` string (property or method name)
	 * `test-value` mixed (value to compare to)
	 * `filter-operaration` string (the comparison operation to perform)
	 *		filter-op is options, defaults to == for values, in_array for arrays
	 * 
	 * @param mixed $argObj
	 * @return array
	 */
    public function filter($argObj) {
		$argObj = $this->NormalizeArgs(func_get_args());
		if (!$argObj->hasAccessNodeObject('filter')) {
//			pr($this->layerName());
			$argObj->setLayer($this->layerName());
		}
		$comparison = $this->selectComparison($argObj->valueOf('filterOperator'));
        $set = collection($this->_data);
		
        $results = $set->filter(function ($entity, $key) use ($argObj, $comparison) {
				$actual = $argObj->accessNodeObject('filter')->value($entity);
				return $comparison($actual, $argObj->valueOf('filterValue'));
            })->toArray(); 
        return $results;
    }
	
	/**
	 * Choose a comparison function based on a provided operator
	 * 
	 * An unknown operator will yield a function that never finds matches
	 * 
	 * @param string $operator
	 * @return function
	 */
	public function selectComparison($operator) {
		$ops = [
			'bad_op' => function($actual, $test_value) { return FALSE; },
			'==' => function($actual, $test_value) { return $actual == $test_value; },
			'!=' => function($actual, $test_value) { return $actual != $test_value; },
			'===' => function($actual, $test_value) { return $actual === $test_value; },
			'!==' => function($actual, $test_value) { return $actual !== $test_value; },
			'<' => function($actual, $test_value) { return $actual < $test_value; },
			'>' => function($actual, $test_value) { return $actual > $test_value; },
			'<=' => function($actual, $test_value) { return $actual <= $test_value; },
			'>=' => function($actual, $test_value) { return $actual >= $test_value; },
			'true' => function($actual, $test_value) { return $actual === TRUE; },
			'false' => function($actual, $test_value) { return $actual === FALSE; },
			'in_array' => function($actual, $test_value) {return in_array($actual, $test_value);},
			'truthy' => function($actual, $test_value) {return (boolean) $actual; }
		];
			
		if (!array_key_exists($operator, $ops)) {
			return $ops['bad_op'];
		} else {
			return $ops[$operator];
		}
		
	}
	/**
	 * Get a key => value map from some or all of the stored entities
	 * 
	 * Filtering is performed by Layer::load() (see the docs) 
	 * $key must be a visible property of the entities
	 * $value must be either a visible property or a method that needs no args
	 * 
	 * @param string $key The property to use for the result array keys
	 * @param string $value The property or method to provide the result values
	 * @param string $type First arg passed to $this->load() ('all' or 'first')
	 * @param array $options Search conditions passed to $this->load()
	 * @return array 
	 */
//	public function keyedList($key, $value, $type = 'all', $options =[]) {
	public function keyedList(LayerAccessArgs $args) {
		$validKey = $this->has($key);
		$valueIsProperty = $validValue = $this->has($value);
		if (!$valueIsProperty) {
			$valueIsMethod = $validValue = method_exists($this->className(), $value);
		}
		
		if(!$validKey || !$validValue) {
			return [];
		}
		
		$result = [];
		$argObj = $this->accessArgs(); // THIS IS UNIMPLEMENTED;
		$data = $this->load($argObj); // THIS IS UNIMPLEMENTED
		foreach ($data as $datum) {
			$result[$datum->$key] = $valueIsProperty ? $datum->$value : $datum->$value();
		}
		
		return $result;
	}
	
    /**
     * Get an array of the IDs of the stored entities
     * 
     * @return array
     */
    public function IDs($layer = null) {
        return array_keys($this->load());
    }
	
	public function unwrap() {
		return $this->_data;
	}
    
    /**
     * Get the records with a matching foreign key value
     * 
     * <code>
     * $pieces->linkedTo('format', 434)
     * </code>
     * 
     * @todo this will only work on belongsTo associations where the entity 
     *      were searching has a property like 'artwork_id'. It's an open question 
     *      whether some entities will carry the join table from a HABTM 
     *      association. If some do, and if we need to use the data, it is usually 
     *      found in a nested layer on an entity property. Two main questions 
     *      then; 1) how would we insure it always came in with the data 
     *      2) would it be more convenient to use mapper/reducer functions to 
     *      move it up out of the (somewhat messy) native nest structure?
     * 
     * @param string $layer The simple name of the associate (eg: artwork, format)
     * @param string $id The foreign key value to match
     * @return null|array
     */
    public function linkedTo($foreign, $foreign_id, $linked = null) {
        $foreign_key = $this->_modelKey($foreign);
        if (!$this->has($foreign_key)) {
            return NULL;
        }
        return $this->filter($foreign_key, $foreign_id);
    }
    
//    /**
//     * Provide single column search
//     * 
//     * <code>
//     *  $formats->filter('title', 'Boxed Set');
//     *  $pieces->filter('number', 12);
//	 *  $pieces->filter('number', [6, 8, 10]);
//     * </code>
//     * 
//     * @param string $property The property to examine
//     * @param mixed $value The value or array of values to search for
//     * @return array An array of entities that passed the test
//     */
//    public function filter($property, $value) {
//        if (!$this->verifyProperty($property)) {
//            return [];
//        }
//        $set = new Collection($this->_data);
//        $results = $set->filter(function ($entity, $key) use ($property, $value) {
//				if (is_array($value)) {
//					return in_array($entity->$property, $value);
//				}
//                return $entity->$property == $value;
//            })->toArray(); 
//        return $results;
//    }
    
    /**
     * Provide single column sorting
     * 
     * <code>
     *  $artworks->sort('title');
     *  $pieces->sort('number', SORT_ASC, SORT_NUMERIC);
     * </code>
     * 
     * @param string $property Name of the property to sort by
     * @param string $dir SORT_ASC or SORT_DESC
     * @param string $type sort type constants
	 * @see https://book.cakephp.org/3.0/en/core-libraries/collections.html#Cake\Collection\Collection::sortBy
     * @return array Array of entities
     */
    public function sort($property, $dir = \SORT_DESC, $type = \SORT_NUMERIC) {
        $set = new Collection($this->_data);
        $sorted = $set->sortBy($property, $dir, $type)->toArray();
        $result = [];
        //indexes are out of order
        foreach ($sorted as $entity) {
            $result[] = $entity;
        }
        return $result;
    }
    
// <editor-fold defaultstate="collapsed" desc="Protected and Private">

    /**
     * Does the $property exist in this layer?
	 * 
	 * This checks against visible properties, echos Entity::has()
     * 
     * @param string $property
     * @return boolean
     */
    public function has($property) {
        return in_array($property, $this->_entityProperties);
    }
	
	public function hasElements() {
		return $this->count() > 0;
	}


    /**
     * Store all the provided entities indexed by id
     * 
     * @param array $entities
     * @throws BadClassConfigurationException
     */
    private function _initEntitySet($entities) {
        foreach ($entities as $key => $entity) {
            if (!strpos(get_class($entity), $this->_className)) {
                $badClass = get_class($entity);
                $message = "All entities stored in a StackLayer must be of "
                    . "the same class. $this->_className was being used "
                    . "when $badClass was encountered.";
                throw new BadClassConfigurationException($message);
            }
            if (!isset($entity->id)) {
                $message = "StackLayer expects to find \$entity->id. This "
                    . "property was missing on array element $key.";
                throw new BadClassConfigurationException($message);
            }
            $this->_data[$entity->id] = $entity;
        }
    }


    /**
     * Set the layer type and entity name for the object
     * 
     * @param array $entities
     * @param string $type
     * @throws BadClassConfigurationException
     */
    private function _initClassProperties($entities, $type) {
        if (!empty($entities)) {
            $keys = array_keys($entities);
            $sampleData = $entities[$keys[0]];
            if (!is_object($sampleData) || !($sampleData instanceof \Cake\ORM\Entity)) {
                $badClass = get_class($sampleData);
                ;
                $message = 'StackLayer class can only accept objects that '
                    . 'extend Entity. The first object in the array is a $badClass '
                    . 'and does not extend Cake\ORM\Entity.';
                throw new BadClassConfigurationException($message);
            }
            $name = namespaceSplit(get_class($sampleData))[1];
        } else {
            if ($type === null) {
                $message = 'If no entities are provided, the name of the expected '
                    . 'entity type must be provided to the StackLayer class as the '
                    . 'second argument to __construct().';
                throw new BadClassConfigurationException($message);
            }
            $name = ucfirst($this->_singularName($type));
        }
        $this->_className = $name; //$this->_entityName($name);
        $this->_layer = strtolower($this->_camelize($this->_className));
        if (empty($sampleData)) {
            $class = "\App\Model\Entity\\$this->_className";
            $sampleData = new $class;
        }
        $this->_entityProperties = $sampleData->visibleProperties();
    }

// </editor-fold>
    
}


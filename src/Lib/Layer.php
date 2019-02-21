<?php
namespace App\Lib;

use Cake\Core\ConventionsTrait;
use Cake\ORM\Enitity;
use Cake\Collection\Collection;
use App\Exception\BadClassConfigurationException;
use \App\Interfaces\LayerAccessInterface;
use App\Model\Traits\LayerAccessTrait;
use App\Model\Lib\LayerAccessArgs;

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
	 * @return string
	 */
    public function entityClass() {
        return $this->_className;
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
    
	public function element($number) {
		if ($number <= $this->count()) {
			return $this->_data[$this->IDs()[$number]];
		}
		return null;
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
		
		$validKey = $this->_verifyProperty($key);
		$valueIsProperty = $validValue = $this->_verifyProperty($value);
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
    public function IDs($args = null) {
        return array_keys($this->_data);
    }
    
    public function distinct($property) {
        if (!$this->_verifyProperty($property)) {
            return [];
        }
//        osd($this->_entities[965]);;
        $set = new Collection($this->_data);
        $asKeys = $set->reduce(function ($accumulated, $entity) use ($property){
                return $accumulated += [$entity->$property => True];
             }, []);
        return array_keys($asKeys);
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
    public function linkedTo($layer, $id) {
        $property = $this->_modelKey($layer);
        if (!$this->_verifyProperty($property)) {
            return NULL;
        }
        return $this->filter($property, $id);
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
//        if (!$this->_verifyProperty($property)) {
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
     * Does the $property exist in this entity?
	 * 
	 * This checks against visible properties
     * 
     * @param string $property
     * @return boolean
     */
    protected function _verifyProperty($property) {
        return in_array($property, $this->_entityProperties);
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
            $name = $type;
        }
        $this->_className = $this->_entityName($name);
        $this->_layer = strtolower($this->_camelize($this->_className));
        if (empty($sampleData)) {
            $class = "\App\Model\Entity\\$this->_className";
            $sampleData = new $class;
        }
        $this->_entityProperties = $sampleData->visibleProperties();
    }

// </editor-fold>
    
}


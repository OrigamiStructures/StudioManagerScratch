<?php
namespace App\Model\Lib;

use App\Interfaces\LayerAccessInterface;
use App\Interfaces\LayerStructureInterface;
use App\Model\Lib\LayerAccessArgs;
use App\Model\Traits\LayerElementAccessTrait;
use Cake\Core\ConventionsTrait;
use Cake\ORM\Enitity;
use Cake\Collection\Collection;
use App\Exception\BadClassConfigurationException;
use \App\Interfaces\xxxLayerAccessInterface;
use App\Model\Traits\LayerAccessTrait;
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
class Layer implements LayerStructureInterface, \Countable {

    use ConventionsTrait;
	use LayerAccessTrait;
	use ErrorRegistryTrait;
	use LayerElementAccessTrait;

    //<editor-fold desc="************** Properties **************">
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
    //</editor-fold>

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
     * Gather the available data at this level and package the iterator
     *
     * @param $name string
     * @return LayerProcessor
     */
    public function getLayer($name = null)
    {
        $Iterator = new LayerProcessor($this->layerName());
        return  $Iterator->insert($this->_data);
    }

    public function getData()
    {
        return $this->_data;
    }

    /**
     * Get an array of the IDs of the stored entities
     *
     * @return array
     */
    public function IDs($layer = null) {
        return array_keys($this->getData());
    }

    //<editor-fold desc="************** Introspection **************">
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

    //</editor-fold>

    //<editor-fold desc="************** OLD LAA Access Methods **************">
    /**
	 * Perform data load from Layer context
	 *
	 * No args gets the id-indexed array of all stored entities
	 * Arg [lookup-index] gets the entity stored under that id/index value
	 *		if the index is invalid, an empty array is returned
	 * If a filter is set, the data is filtered, then paginated and returned
	 * Otherwise, the full set is paginated and returned
	 *
	 * @param LayerAccessArgs|null $argObj
	 * @return array
	 */
	public function load($argObj = null) {
		if(is_null($argObj)) {
			return $this->_data;
		}

		$this->verifyInstanceArgObj($argObj);

		if ($argObj->isFilter()) {
			$result = $this->filter($argObj);
		} else {
			$result = $this->_data;
		}

		return $this->paginate($result, $argObj);

	}

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
        //indexes are out of order and could be confusing
        return array_values($sorted);
    }
    //</editor-fold>

    //<editor-fold desc="************************* Advanced Features *************************">
    /**
	 * Filter this layers set of entities
	 *
	 * Supply an LayerAccessArg object with a `specifyFilter()` done or provide
	 * `value-source` string (property or method name)
	 * `test-value` mixed (value to compare to)
	 * `filter-operaration` string (the comparison operation to perform)
	 *		filter-op is options, defaults to == for values, in_array for arrays
	 *
	 * @param LayerAccessArgs $argObj
	 * @return array
	 */
    public function filter($argObj) {
		$argObj = $this->NormalizeArgs(func_get_args());
		if (!$argObj->hasAccessNodeObject('filter')) {
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
    //</editor-fold>

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
     * @return array
     */
    public function linkedTo($foreign, $foreign_id) {
        $foreign_key = $this->_modelKey($foreign);
        if (!$this->has($foreign_key)) {
            return [];
        }
        return $this->getLayer()
            ->NEWfind()
            ->specifyFilter($foreign_key, $foreign_id)
            ->toArray();
    }

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

// <editor-fold defaultstate="collapsed" desc="************** Protected and Private **************">
    /**
     * Choose a comparison function based on a provided operator
     *
     * An unknown operator will yield a function that never finds matches
     *
     * @param string $operator
     * @return callable
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
            'in_array' => function($actual, $test_values) {return in_array($actual, $test_values);},
            'truthy' => function($actual, $test_value) {return (boolean) $actual; }
        ];

        if (!array_key_exists($operator, $ops)) {
            return $ops['bad_op'];
        } else {
            return $ops[$operator];
        }

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
                    . "property was missing on array element $key. Did you "
						. "forget to name a layer when doing loadStack?";
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
                break;
            default:
//				pr(func_get_args());//die;
//				throw new \BadMethodCallException('Bad arguments for Layer::filter() provided.');
                break;
        }
        return $argObj;
    }
// </editor-fold>

}

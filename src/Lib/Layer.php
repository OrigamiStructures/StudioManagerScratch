<?php
namespace App\Lib;

use Cake\Core\ConventionsTrait;
use Cake\ORM\Enitity;
use Cake\Collection\Collection;

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
class StackLayer {
    
    use ConventionsTrait;
    
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
    protected $_entities = [];
    
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
    public function has($id) {
        return isset($this->_entities[$id]);
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
        $set = new Collection($this->_entities);
        $result = $set->reduce(function ($accumulated, $entity) {
                return $accumulated && !($entity->isDirty());
             }, TRUE);
        return $result;
    }
    
    /**
     * The StackLayer's version of a find() 
     * 
     * Supports some simple filtering and sorting
     * 
     * <code>
     * $editions->get(312);  //edition->id = 312
     * $artworks->get('title', 'Yosemite'); //atwork->title = 'Yosemite'
     * $pieces->get('all');  //return all stored entities
     * $pieces->get('first); //return the first stored entity
     * $pieces->get('first', ['edition_id', 455]); //first where piece->edition_id = 455
     * </code>
     * 
     * ### Be careful, this will return references to the entities. Any changes 
     *      to them will ripple back into this package. And this class was designed 
     *      for access by rendering processes, not edit-clycle processes.
     * 
     * @param string $id
     * @return Entity
     */
    public function get($type, $options = []) {
        $types = ['all', 'first'];

        // arbitrary exposed value on $type, assumed to be an id
        if (!in_array($type, $types + $this->_entityProperties)) {
            $type = $id;
            if (!$this->has($id)) {
                return NULL;
            }
            return $this->_entities[$id];
        }
        
        // property name on type and some value on options will filter to prop = value
        if (in_array($type, $this->_entityProperties) && !is_array($options)) {
            return $this->filter($type, $options);
        }
        
        // if not a listed type, not a valid argument
        if (!in_array($type, $types)) {
            return null;
        }
        
        // left with one of the three $types
        if ($type === 'all') {
            return $this->_entities;
        }
        
        if ($type === 'first') {
            if (empty($options)) {
                return $this->_entities[$this->IDs()[0]];
            }
            if (!is_array($options)) {
                return null;
            }
            $property = array_keys($options)[0];
            $value = $options[$property];
            $result = $this->filter($property, $value);
            return array_shift($result);
        }
        return null;
    }
    
    /**
     * The count of stored entities in this layer
     * 
     * @return int
     */
    public function count() {
        return count($this->_entities);
    }
    
    /**
     * Get an array of the IDs of the stored entities
     * 
     * @return array
     */
    public function IDs() {
        return array_keys($this->_entities);
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
        if (!$this->_verifyPropertyExists($property)) {
            return NULL;
        }
        return $this->filter($property, $id);
    }
    
    /**
     * Provide single column search
     * 
     * <code>
     *  $formats->filter('title', 'Boxed Set');
     *  $pieces->filter('number', 12);
     * </code>
     * 
     * @param type $property
     * @param type $value
     * @return type
     */
    public function filter($property, $value) {
        if ($this->_verifyProperty($property)) {
            return NULL;
        }
        $set = new Collection($this->_entities);
        $results = $set->filter(function ($entity, $key) use ($type, $value) {
                return $entity->$type === $value;
            })->toArray();
        return $results;
    }
    
    /**
     * Provide single column sorting
     * 
     * <code>
     *  $artworks->sort('title');
     *  $pieces->sort('number', SORT_ASC, SORT_NUMERIC);
     * </code>
     * 
     * @param type $property
     * @param type $dir
     * @param type $type
     * @return type
     */
    public function sort($property, $dir = \SORT_DESC, $type = \SORT_NUMERIC) {
        $set = new Collection($this->_entities);
        return $set->sortBy($property, $dir, $type)->toArray();
    }
    
// <editor-fold defaultstate="collapsed" desc="Protected and Private">

    /**
     * Does the $property exist in this entity?
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
            $this->_entities[$entity->id] = $entity;
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
        if (!isEmpty($entities)) {
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
            $name = namespaceSplit(get_class($sampleData));
        } else {
            if (isNull($type)) {
                $message = 'If no entities are provided, the name of the expected '
                    . 'entity type must be provided to the StackLayer class as the '
                    . 'second argument to __construct().';
                throw new BadClassConfigurationException($message);
            }
            $name = $type;
        }
        $this->_className = $this->_entityName($name);
        $this->_layer = $this->_camelize($this->_className);
        $this->_entityProperties = $sampleData->visibleProperties();
    }

// </editor-fold>
    
}

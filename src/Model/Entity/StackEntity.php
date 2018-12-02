<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Lib\Layer;

/**
 * Stacks
 * 
 * Tools to manage multiple Layer object properties in a containing object
 * 
 * Provide reporting tools to see what records are stored in the contained 
 * entity stacks.
 * 
 * Provide accessor tools to extract contained objects with explicit looping 
 * 
 * @author Main
 */
class StackEntity extends Entity {
    
    /**
     * Is the id a member of the set
     * 
     * @todo Overlap with Entity has() method. Resolve our name strategy
     * 
     * @param string $id
     * @return boolean 
     */
    public function exists($layer, $id) {
        $property = $this->_getLayerProperty($layer);
        if ($property) {
            return $property->has($id);
        }
        return FALSE;
    }
    
    /**
     * Get entities from one of the layers
     * 
     * This call supports all the `get` methods as Layer, but it requires a 
     * new first argument naming the layer to operate on. The second argument 
     * represents the signature for the Layer::get(). If your chosen Layer 
     * search has one arg, you may pass it exposed as the second arg or in 
     * an array.
     * 
     * If your Layer call has more than one argument, pass it as an array with 
     * two elements that match the arguments of your Layer::get()
     * 
     * <code>
     * get('editions', [312]);  //edition->id = 312
     * get('editions', 312);  //edition->id = 312
     * get('artworks', ['title', 'Yosemite']); //atwork->title = 'Yosemite'
     * get('pieces', ['all']);  //return all stored entities
     * get('pieces', 'all');  //return all stored entities
     * get('pieces', 'first); //return the first stored entity
     * </code>
     * 
     * @todo overlap with Entity method. Resolve our naming
     * 
     * @param string $layer
     * @param mixed $options
     * @return array
     */
    public function load($layer, $options = []) {
        $property = $this->_getLayerProperty($layer);
        if (!$property) {
            return [];
        }

		//arrange params for call to layer->get()
        $opts = [];
        if (is_array($options)) {
            $type = array_shift($options);
            if (!empty($options)) {
                $opts = array_shift($options);
            }
        } else {
            $type = $options;
        }
        
        return $property->load($type, $opts);
    }
	
    /**
     * Get the count of entities in a layer
     * 
     * @param string $layer
     * @return int
     */
    public function count($layer) {
        $property = $this->_getLayerProperty($layer);
        if ($property) {
            return $property->count();
        }
        return 0;
    }
    
    public function hasNo($layer) {
        return $this->count($layer) === 0;
    }
	
	/**
	 * Get the name of the primary layer in the stack
	 * 
	 * @return string
	 */
	public function primaryLayer() {
		return $this->_primary;
	}
	
	/**
	 * Get the id of the primary entity in the stack
	 * 
	 * @return string
	 */
	public function primaryId() {
		return $this->IDs($this->_primary)[0];
	}
	
	/**
	 * Get the primary entity in the stack
	 * 
	 * @return Entity
	 */
	public function primaryEntity() {
		$primary = $this->_getLayerProperty($this->_primary)->load('all');
		return array_shift($primary);
	}
    
    /**
     * Get all the distinct values form the properties in a layer's entities
     * 
     * @param string $layer
     * @param string $property
     * @return array
     */
    public function distinct($layer, $property) {
        $object = $this->_getLayerProperty($layer);
        if ($object) {
            return $object->distinct($property);
        }
        return [];
    }
    
    /**
     * Get the ids of all the entities in a layer
     * 
     * @param string $layer
     * @return array
     */
    public function IDs($layer) {
        $property = $this->_getLayerProperty($layer);
        if ($property) {
            return $property->IDs();
        }
        return [];
    }
    
    /**
     * In a layer, get the entities linked to a specified record
     * 
     * @param string $layer
     * @param array $options
     * @return array
     */
    public function linkedTo(string $layer, array $options) {
        $property = $this->_getLayerProperty($layer);
        if ($property && count($options) === 2) {
            return $property->load($options[0], $options[1]);
        }
        return [];
    }
 
    /**
     * If the layer property is init'd with a Layer, return it
     * 
     * @param string $layer Name of the layer
     * @return boolean|Layer
     */
    protected function _getLayerProperty($layer) {
        $property = $this->$layer;
        if (isset($property) && $property instanceof Layer) {
            return $property;
        }
        return FALSE;
    }

}

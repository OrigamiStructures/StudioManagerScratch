<?php
namespace App\Lib\Traits;

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
trait StackTrait {
    
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
            return $property->exists($id);
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
     * get('pieces', ['first', ['edition_id', 455]]); //first where piece->edition_id = 455
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

        $opts = [];
        if (is_array($options)) {
            $type = array_shift($options);
            if (count($options) > 1) {
                $opts = array_shift($options);
            }
        } else {
            $type = $options;
        }
        
        return $property->get($type, $opts);
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
    
    /**
     * Get all the distinct values form the properties in a layer's entities
     * 
     * @param string $layer
     * @param string $property
     * @return array
     */
    public function distinct($layer, $property) {
        $property = $this->_getLayerProperty($layer);
        if ($property) {
            return $property->distinct($property);
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
            return $property->linkedTo($$options[0], $options[1]);
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
        if (isset($this->$property) && $this->$property instanceof Layer) {
            return $this->$property;
        }
        return FALSE;
    }

}

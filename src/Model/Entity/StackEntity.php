<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Lib\Layer;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

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
        $property = $this->get($layer);
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
        $property = $this->get($layer);
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
        $property = $this->get($layer);
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
		$primary = $this->get($this->_primary)->load('all');
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
        $object = $this->get($layer);
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
        $property = $this->get($layer);
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
        $property = $this->get($layer);
        if ($property && count($options) === 2) {
            return $property->load($options[0], $options[1]);
        }
        return [];
    }
 
    /**
     * Adds Layer property empty checks to other native checks
     * 
     * {@inheritdoc}
     *
     * @param string $property The property to check.
     * @return bool
     */
    public function isEmpty($property)
    {
        $value = $this->get($property);
        if (is_object($value) 
            && $value instanceof \App\Lib\Layer 
            && $value->count() === 0
        ) {
            return true;
        }
        return parent::isEmpty($property);
    }
    
    /**
     * Pass through for 'set' to handle Layer type columns
     * 
     * If a layer value is set() directly with an array, this 
     * overwrite will take care of it. New and patch entity do 
     * the correct typing I think. 
     * 
     * {@inheritdoc}
     * 
     * @param Layer $property
     * @param Layer $value
     * @param array $options
     * @return type
     */
    public function set($property, $value = null, array $options = []) {
        $typeMap = TableRegistry::getTableLocator()
            ->get($this->getSource())
            ->getSchema()
            ->typeMap();
        
        if (is_string($property) 
            && Hash::extract($typeMap, $property) === ['layer']
            && !($value instanceof Layer)) {
                $value = $this->makeLayerObject($property, $value);
            
        } elseif (is_array($property)) {
            $typeMap = (Hash::filter($typeMap, function($value){
                    return $value === 'layer';
            }));
            foreach ($typeMap as $p => $unused) {
                if (Hash::check($property, $p)
                    && !($property[$p] instanceof Layer)) {
                        $property[$p] = $this->makeLayerObject($p, $property[$p]);
                }
            }
        }
       return parent::set($property, $value, $options);
    }
    
    private function makeLayerObject($layer, $seed) {
        try {
            $product = new Layer($seed);
            return $product;
        } catch (\Exception $ex) {
            $this->setError($layer, $ex->getMessage());
//            osd($this->getErrors());
            return new Layer([], $layer);
        }
    }
    
}
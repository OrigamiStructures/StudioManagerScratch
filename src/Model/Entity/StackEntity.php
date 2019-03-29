<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Lib\Layer;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use App\Interfaces\LayerAccessInterface;
use App\Model\Traits\LayerAccessTrait;
use App\Model\Lib\LayerAccessArgs;

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
class StackEntity extends Entity implements LayerAccessInterface {
	
	use LayerAccessTrait;
    
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
            return $property->hasId($id);
        }
        return FALSE;
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
    
    public function hasLayer($layer) {
        return $this->count($layer) > 0;
    }
	
	/**
	 * Get the name of the primary layer in the stack
	 * 
	 * @return string
	 */
	public function primaryLayer() {
		return $this->_primary;
	}
	
	public function dataOwner() {
		osd($this->primaryEntity());die;
		return $this->primaryEntity()->user_id;
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
		$allArg = $this->accessArgs()->setLimit('first');
		return $this->get($this->primaryLayer())->load($allArg);
	}
    
	/**
	 * Load data from the StackEntity context
	 * 
	 * If no args are given, return $this in an array indexed by the primary id
	 * If a layer is named, it should be a property of this stack. If its 
	 *	not a valid Layer type property, an empty array is returned. 
	 * Given a valid property/layer the  query is delegated to that named layer. 
	 *	The layer will do all required filtering and pagination. StackEntity 
	 *	will return that result
	 * 
	 * @param LayerAccessArgs $argObj
	 * @return array
	 */
	public function load(LayerAccessArgs $argObj = null) {
		
		if (is_null($argObj)) {
			return [$this->primaryId() => $this];
		}
		
        $property = $this->getValidPropery($argObj);
        if (!$property) {
            return [];
        }

		return $property->load($argObj);
		
	}
	
	/**
	 * Get a property of this stack that is a Layer
	 * 
	 * The target is named in 'layer' of the arg object, but it is seen as a 
	 * property of the stack. ('property' in the argObj is of the layer entities) 
	 * 
	 * 
	 * @param LayerAccessArgs $argObj
	 * @return boolean|Layer Layer object if valid, FALSE otherwise
	 */
	private function getValidPropery($argObj) {
		$property = $argObj->hasLayer() ? $this->get($argObj->valueOf('layer')) : FALSE;
		if($property && is_a($property, '\App\Model\Lib\Layer')) {
			return $property;
		} else {
			return FALSE;
		}
	}
    /**
     * Get this primary id or the IDs of all the entities in a layer
     * 
     * @param string $layer
     * @return array
     */
    public function IDs($layer = null) {
		if (is_null($layer)) {
			return array_keys($this->load());
		}
		
        $property = is_null($layer) ? null : $this->get($layer);
        if (is_null($property) || !is_a($property, '\App\Model\Lib\Layer')) {
            return [];
        }

        return $property->IDs();
	}

	public function distinct($property, $layer = '') {
        $args = $this->normalizeDistinctArgs($property, $layer);
		if (!$args) {
			return [];
		}
		if($args->ValueSource->isValid()) {
			$data = $args->valueOf('layer')->members();
			return $this
				->{$args->valueOf('layer')}
				->trait_distinct($data, $args);
		}
		return [];
	}
    
    private function normalizeDistinctArgs($property, $layer) {
		
        if (is_string($property)) {
			if (!$this->hasLayer($layer)) {
				return FALSE;
			}
            if(empty($layer)) {
                throw new BadMethodCallException(
                    'a layer must be specified for distinct() on a StackEntity');
            } else {
                $args = $this->accessArgs()
                    ->setLayer($layer)
                    ->setValueSource($property);
            }
        } else {
            $args = $property;
        }
        return $args;
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
            && $value instanceof \App\Model\Lib\Layer 
            && $value->count() === 0
        ) {
            return true;
        }
        return parent::isEmpty($property);
    }
	
// <editor-fold defaultstate="collapsed" desc="LAYER ACCESS INTERFACE REALIZATION">

	/**
	 * In a layer, get the entities linked to a specified record
	 * 
	 * @param string $layer
	 * @param array $options
	 * @return array
	     */
	public function linkedTo($foreign, $foreign_id, $linked = null) {
		if($this->has($linked)) {
			return $this->$linked->linkedTo($foreign, $foreign_id);
		}
		return [];
	}

	public function keyedList(LayerAccessArgs $argObj) {
		;
	}
	
	public function filter($property, $value) {
		;
	}
	
// </editor-fold>


	    
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

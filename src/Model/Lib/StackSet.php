<?php
namespace App\Model\Lib;

use App\Model\Entity\StackEntity;
use App\Interfaces\LayerAccessInterface;
use App\Model\Traits\LayerAccessTrait;
use App\Model\Lib\LayerAccessArgs;

/**
 * StackSet
 * 
 * This is a collector class which holds sets of Entities that extend StackEntity
 * 
 * This class provides access to the stored entities and thier data 
 * to make it easier to pull out stacks, layers, and merged collections of 
 * enties from multiple stack.
 *
 * @author dondrake
 */
class StackSet implements LayerAccessInterface {
	
	use LayerAccessTrait;
	
	protected $_stacks = [];
	
	protected $_stackName;

	
	/**
	 * Add another entity to the collection
	 * 
	 * @param string $id
	 * @param StackEntity $stack
	 */
	public function insert($id, $stack) {
		$this->_stacks[$id] = $stack;
		if (!isset($this->_stackName)) {
			$this->_stackName = $stack->primaryLayer();
		}
	}
	
	/**
	 * Return all the entities in an array
	 * 
	 * @return array
	 */
	public function all() {
		return $this->_stacks;
	}
	
	/**
	 * Get the IDs of all the primary entities in the stored stack entities
	 * 
	 * @return array
	 */
	public function members() {
		return array_keys($this->_stacks);
	}
	
	public function element($number) {
		if ($number <= $this->count()) {
			return $this->_stacks[$this->members()[$number]];
		}
		return null;
	}
	
	public function member($id) {
		if (in_array($id, $this->members())) {
			return $this->_stacks[$id];
		}
		return null;
	}
    
    /**
     * Get the count of stored Stack objects
     * 
     * @return integer
     */
    public function count() {
        return count($this->_stacks);
    }
	
	/**
	 * Is this the id of one of the primary enities in a stored stack entity
	 * 
	 * @param string $id
	 * @return boolean
	 */
	public function isMember($id) {
		return array_key_exists($id, $this->_stacks);
	}
	
	/**
	 * Return all StackEntities that contain a layer entity with id = $id
	 * 
	 * @param string $layer
	 * @param string $id
	 * @return array
	 */
	public function ownerOf($layer, $id, $set = 'all') {
		$stacks = [];
		foreach ($this->_stacks as $stack) {
			if ($stack->exists($layer, $id)) {
				$stacks[] = $stack;
			}
		}
        if ($set === 'first' && count($stacks) > 0) {
            $stack = array_shift($stacks);
            return $stack;
        }
		return $stacks;
	}
	
	/**
	 * Get all the ids accross all the stored StackEntities for the Layer entities
	 * 
	 * This is a collection-level method that matches the StackEntity's and Layer's 
	 * IDs() methods. These form a pass-through chain. 
	 * 
	 * Calling IDs() from this level will merge all found results from all 
	 * the stored StackEntities.
	 * 
	 * @param string $layer
	 * @return array
	 */
	public function IDs($layer) {
		$ids = [];
		foreach($this->_stacks as $stack) {
			$ids = array_merge($ids, $stack->IDs($layer));
		}
		return $ids;
	}

// <editor-fold defaultstate="collapsed" desc="LAYER ACCESS INTERFACE REALIZATION">
	
	/**
	 * Get all layer entities that match
	 * 
	 * This is a collection-level method that matches the StackEntity's and Layer's 
	 * load() methods. These form a pass-through chain, so the allowed 
	 * arguments here are the same as for StackEntity::load(). 
	 * 
	 * Calling load from this level will merge all found results from all 
	 * the stored StackEntities.
	 * 
	 * @param string $layer
	 * @param mixed $options
	 * @return array
	 */
	public function load(LayerAccessArgs $argObj) {
		if (is_null($layer)) {
			return $this->_stacks;
		}
		if ($argObj->valueOf('limit') === 1 && $argObj->valueOf('property') === '') {
			$keys = array_keys($this->_stacks);
			return $this->_stacks[$keys[0]];
		}
		$results = [];
		foreach ($this->_stacks as $stack) {
			$result = $stack->load($argObj);
			$results = array_merge($results, (is_array($result) ? $result : [$result]));
		}
		
		return $results;
	}

	public function keyList($key, $value, $layer, $options) {
		
	}

	public function distinct($propery) {
		
	}

	public function filter($property, $value) {
		
	}

	public function keyedList($key, $value, $type, $options) {
		
	}

	public function linkedTo($layer, $id) {
		
	}

// </editor-fold>
	
}

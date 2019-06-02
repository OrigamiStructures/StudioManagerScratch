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
 * This class provides access to the stored entities and their data 
 * to make it easier to pull out stacks, layers, and merged collections of 
 * entities from multiple stack.
 *
 * @author dondrake
 */
class StackSet implements LayerAccessInterface {
	
	use LayerAccessTrait;
	
	protected $_data = [];
	
	protected $_stackName;

	/**
	 * Add another entity to the collection
	 * 
	 * @param string $id
	 * @param StackEntity $stack
	 */
	public function insert($id, $stack) {
		$this->_data[$id] = $stack;
		if (!isset($this->_stackName)) {
			$this->_stackName = $stack->rootLayerName();
		}
	}
	
	/**
	 * Return all the entities in an array
	 * 
	 * @return array
	 */
	public function all() {
		return $this->_data;
	}
	
	/**
	 * Perform data load from StackSet context
	 * 
	 * No args will get the id-indexed array of stack entities
	 * No layer specified will get the paginated chunck of the stack entity array
	 * Once a layer is specified, load will deligate to each stack entity 
	 * in turn. Filtering and pagination will be done, and the accumulated 
	 * result will be returned
	 * 
	 * @param LayerAccessArgs $argObj
	 * @return array
	 */
	public function load(LayerAccessArgs $argObj = null) {
		
		if (is_null($argObj)) {
			return $this->_data;
		}
		
		if (!$argObj->hasLayer()) {
			return $this->paginate($this->_data, $argObj);
		} else {
			$result = [];
			foreach ($this->_data as $stack) {
				$found = $stack->load($argObj);
				$result = array_merge($result, (is_array($found) ? $found : [$found]));
			}
		}
		
		return $result;
		
	}
	
    /**
     * Get the count of stored Stack objects
     * 
     * @return integer
     */
    public function count() {
        return count($this->_data);
    }
	
	/**
	 * Is this the id of one of the primary enities in a stored stack entity
	 * 
	 * @param string $id
	 * @return boolean
	 */
	public function isMember($id) {
		return array_key_exists($id, $this->_data);
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
		foreach ($this->_data as $stack) {
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
	
// <editor-fold defaultstate="collapsed" desc="LAYER ACCESS INTERFACE REALIZATION">
	
	/**
	 * Get all the ids accross all the stored StackEntities or the Layer entities
	 * 
	 * This is a collection-level method that matches the StackEntity's and Layer's 
	 * IDs() methods. These form a pass-through chain. 
	 * 
	 * Calling IDs() from this level will insure unique results if 
	 * Layer IDs are pulled. 
	 * 
	 * StackEntity IDs will be from the primary entity propery and will
	 * be unique becuase the set structure insures it.
	 * 
	 * @param string $layer
	 * @return array
	 */
	public function IDs($layer = null) {
		if(is_null($layer)){
			return array_keys($this->load());
		}
		$ids = [];
		foreach($this->_data as $stack) {
			$ids = array_merge($ids, $stack->IDs($layer));
		}
		return array_unique($ids);
	}

	public function keyedList(LayerAccessArgs $argObj) {
		
	}

	public function filter($property, $value) {
		debug('other strike');
	}

	public function linkedTo($foreign, $foreign_id, $linked = null) {
		$accum = [];
		foreach ($this->_data as $stack) {
			$result = $stack->linkedTo($foreign, $foreign_id, $linked);
			$accum = array_merge($accum, $result);
		}
		return $accum;
	}

// </editor-fold>
	
}

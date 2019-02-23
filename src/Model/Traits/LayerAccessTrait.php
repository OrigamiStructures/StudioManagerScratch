<?php
namespace App\Model\Traits;

use App\Model\Lib\LayerAccessArgs;
use App\Lib\Layer;

/**
 * Description of LayerAccessTrait
 *
 * @author dondrake
 */
trait LayerAccessTrait {
	
	public function accessArgs() {
		return new LayerAccessArgs();
	}
		
	public function layer(array $entities) {
		return new Layer($entities);
	}
    
//	public function all($property);
//	
	public function distinct($property, $layer = ''){
		
	}
	
	public function load(LayerAccessArgs $argObj) {
		
		if (is_a($this, '\App\Model\Lib\StackSet')) {
			return $this->loadStackSet($argObj);
		}
		
		if (is_a($this, '\App\Model\Entity\StackEntity')){
			return $this->loadStackEntity($argObj);
		}
		
		if (is_a($this, '\App\Lib\Layer')) {
			return $this->loadLayer($argObj);
		}
		
		throw new \App\Exception\BadClassConfigurationException(
				"LayerAccessTrait is only expected to appear in StackSet, "
				. "StackEntity, Layer, or a subclass of one of these.");
	}
	
	public function keyedList(LayerAccessArgs $args){
		
	}
//	
	public function linkedTo($foreign, $foreign_id, $linked = null){
		
	}
	
	public function IDs($args = null) {
		
	}
	
    /**
     * Provide single column search
     * 
     * <code>
     *  $formats->filter('title', 'Boxed Set');
     *  $pieces->filter('number', 12);
	 *  $pieces->filter('number', [6, 8, 10]);
     * </code>
     * 
     * @param string $property The property to examine
     * @param mixed $value The value or array of values to search for
     * @return array An array of entities that passed the test
     */
    public function filter($property, $value) {
        if (!is_a($this, '\App\Lib\Layer') || !$this->verifyProperty($property)) {
            return [];
        }
        $set = collection($this->_data);
        $results = $set->filter(function ($entity, $key) use ($property, $value) {
				if (is_array($value)) {
					return in_array($entity->$property, $value);
				}
                return $entity->$property == $value;
            })->toArray(); 
        return $results;
    }
    
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
	private function loadStackSet($argObj) {
		
//		if (is_a($this, '\App\Model\Lib\StackSet')) {
//			
//			if(!$argObj->valueOf(layer)) {
//				return $this->paginate($this->stacks);
//			} else {
//				$result = [];
//				foreach ($this->stacks as $stack) {
//					$result[] = $stack->load($argObj);
//				}
//				return $result;
//			}
//			
//		} elseif(is_a($this, '\App\Model\Entity\StackEntity')) {
//			
//			if (!$argObj->valueOf('layer')) { return []; }
//			return $property->load($argObj);
//			
//		} elseif (is_a($this, '\App\Lib\Layer')) {
//			
//			$index = $argObj->valueOf('lookup_index');
//			if ($this->hasId($index)) {
//				return $this->_data[$id];
//			}
//
//			if ($argObj->isFilter()) {
//				$result = $this->filter($argObj->valueOf('property'), $argObj->valueOf('filter_value'));
//			} else {
//				$result = $this->_data;
//			}
//			return $this->paginate($result);
//
//		}
//		
		if (!$argObj->valueOf('layer')) {
			return $this->_data;
		}
		if ($argObj->valueOf('limit') === 1 && !$argObj->valueOf('layer')) {
			$keys = array_keys($this->_data);
			return $this->_data[$keys[0]];
		}
		$results = [];
		foreach ($this->_data as $stack) {
			$result = $stack->load($argObj);
			$results = array_merge($results, (is_array($result) ? $result : [$result]));
		}
		
		return $results;
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
	private function loadStackEntity($argObj) {
		
        $property = $argObj->valueOf('layer') ? $this->get($argObj->valueOf('layer')) : FALSE;
        if (!$property) {
            return [];
        }

		return $property->load($argObj);
	}
	
    /**
     * The StackLayer's version of a find() 
     * 
     * Supports some simple filtering and sorting
     * 
     * <code>
     * $editions->get(312);  //edition->id = 312
     * $artworks->get('title', 'Yosemite'); //atwork->title = 'Yosemite'
	 * $artworks->get('title', ['Yosemite', 'Yellowstone']
     * $pieces->get('all');  //return all stored entities
     * $pieces->get('first); //return the first stored entity
     * $pieces->get('first', ['edition_id', 455]); //first where piece->edition_id = 455
     * </code>
     * 
     * ### Be careful, this will return references to the entities. Any changes 
     *      to them will ripple back into this package. And this class was designed 
     *      for access by rendering processes, not edit-clycle processes.
	 * 
	 * @todo How about making a 'not' type search? ('not', ['edition_id', 455])
     * 
     * @param string $type 'all', 'first', an ID, a property name
	 * @param array $options Search arguments
	 * @param LayerAccessArgs a parameters object
     * @return array The entities that passed the test
     */
	private function loadLayer($argObj) {
		
		if ($argObj->valueOf('lookup_index')) {
			$id = $argObj->valueOf('lookup_index');
            if (!$this->hasId($id)) {
                return [];
            }
            return $this->_data[$id];
		}
		
		if ($argObj->isFilter()) {
			$result = $this->filter($argObj->valueOf('property'), $argObj->valueOf('filter_value'));
		} else {
			$result = $this->_data;
		}
		
		if (!$argObj->valueOf('limit') || $argObj->valueOf('limit') == -1) {
			return $result;
		} else {
			return array_shift($result);
		}
	}
	
	private function validIndex($index) {
		return $this->hasId($index);
	}
}

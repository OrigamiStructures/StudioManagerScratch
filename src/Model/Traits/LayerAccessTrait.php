<?php
namespace App\Model\Traits;

use App\Model\Lib\LayerAccessArgs;

/**
 * Description of LayerAccessTrait
 *
 * @author dondrake
 */
trait LayerAccessTrait {
	
	public function accessArgs() {
		return new LayerAccessArgs();
	}
		
//	public function all($property);
//	
//	public function distinct($propery);
//	
//	public function duplicate($property);
//	
//	public function filter($property, $value);
	
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
	
//	public function keyedList($key, $value, $type, $options);
//	
//	public function linkedTo($layer, $id);
	
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
		if (!$argObj->valueOf('layer')) {
			return $this->_stacks;
		}
		if ($argObj->valueOf('limit') === 1 && !$argObj->valueOf('layer')) {
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
            return $this->_entities[$id];
		}
		
		if ($argObj->isFilter()) {
			$result = $this->filter($argObj->valueOf('property'), $argObj->valueOf('comparison_value'));
		} else {
			$result = $this->_entities;
		}
		
		if (!$argObj->valueOf('limit') || $argObj->valueOf('limit') == -1) {
			return $result;
		} else {
			return array_shift($result);
		}
	}
}

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
	
	/**
	 * Return the n-th stored element (starting from 0)
	 * 
	 * Data is stored in id-indexed arrays, but this method will let you 
	 * pluck the n-th item without bothering with the id-indexes
	 * 
	 * @param int $number Array index 0 through n
	 * @return Entity
	 */
	public function element($number){
		$data = array_values($this->load());
		if(count($data) > $number) {
			$result =  $data[$number];
		} else {
			$result = null;
		}
		return $result;
	}
    
//	public function all($property);
//	
	public function distinct($property, $layer = ''){
		
	}
	
	public function keyedList(LayerAccessArgs $args){
		
	}
//	
	public function linkedTo($foreign, $foreign_id, $linked = null){
		
	}
	
	public function IDs($layer = null){
		
	}
	
	public function paginate($data, LayerAccessArgs $argObj) {
		if ($argObj->valueOf('limit') === 1 && !empty($data)) {
			return array_shift($data);
		}
		return $data;
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
        if (!is_a($this, '\App\Lib\Layer') || !$this->has($property)) {
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
    	
}

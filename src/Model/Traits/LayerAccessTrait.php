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
		$result = $this->load($args);
		$collection = collection($this->load($args));
		$collection->reduce(function($accum, $entity) use ($args){
			if($args->valueOf('method')) {
				$method = $args->valueOf('method');
				$value = $entity->$mehtod();
			} else {
				
			}
		}, []);
	}
	
	public function validateSource($entity, $source) {
		return $entity->has($source) && method_exists($this->entityClass('namespaced'), $value_source) ;
	}
	
	public function value($entity, $source) {
		if(in_array($source, $entity->visibleProperties())) {
			$result = $entity->$source;
		} else {
			$result = $entity->$source();
		}
		return $result;
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
}

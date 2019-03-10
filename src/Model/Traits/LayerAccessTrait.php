<?php
namespace App\Model\Traits;

use App\Model\Lib\LayerAccessArgs;
use App\Lib\Layer;
use App\Model\Lib\ValueSource;

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
	
	public function loadKeyValueList(LayerAccessArgs $args){
		$data = $this->load($args);
		$KeySource = $this->args->getKeyObject();
		$ValueSource = $this->args->getSourceObject();
		return $this->keyValueList($data, $KeySource, $ValueSource);
	}
	
	public function loadValueList(LayerAccessArgs $args){
		$data = $this->load($args);
		$ValueSource = $this->args->getSourceObject();
		return $this->valueList($data, $ValueSource);
	}
	
	public function keyValueList($data, ValueSource $KeySource,	ValueSource$ValueSource) {
		$collection = collection($data);
		$collection->reduce(function($accum, $entity) use ($KeySource, $ValueSource){
			$accum[$KeySource->value($entity)] = $ValueSource->value($entity);
			return $accum;
		}, []);
	}
	
	public function valueList($data, ValueSource $valueSource) {
		$collection = collection($data);
		$collection->reduce(function($accum, $entity) use ($ValueSource){
			$accum[] = $ValueSource->value($entity);
			return $accum;
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
	
	public function filter($argObj) {
		
	}

//	
	public function linkedTo($foreign, $foreign_id, $linked = null){
		
	}
	
	public function IDs($layer = null){
		
	}
	
	/**
	 * Make this into a real pagination class?
	 * 
	 * @param array $data
	 * @param LayerAccessArgs $argObj
	 * @return array
	 */
	public function paginate($data, LayerAccessArgs $argObj) {
		if ($argObj->valueOf('limit') === 1 && !empty($data)) {
			return array_shift($data);
		}
		if ($argObj->valueOf('limit') < 1) {
			return $data;
		}
		$paginated = array_chunk($data, $argObj->valueOf('limit'));
		if (count($paginated) < $argObj->valueOf('page')) {
			return $paginated[$argObj->valueOf('page') - 1];
		}
		return $data;
	}
	
}

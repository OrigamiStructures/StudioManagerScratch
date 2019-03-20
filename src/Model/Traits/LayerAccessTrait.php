<?php
namespace App\Model\Traits;

use App\Model\Lib\LayerAccessArgs;
use App\Model\Lib\Layer;
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
    
    public function find() {
        $args = new LayerAccessArgs($this);
        return $args;
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
	public function loadDistinct($property, $layer = ''){
		
	}
	
	/**
	 * Full feature load(), results reduced to key=>value arrry
	 * 
	 * @param LayerAccessArgs $args
	 * @return array
	 */
	public function loadKeyValueList(LayerAccessArgs $args){
		$data = $this->load($args);
		$KeySource = $this->args->keyObject();
		$ValueSource = $this->args->sourceObject();
		return $this->keyValueList($data, $KeySource, $ValueSource);
	}
	
	/**
	 * Full feature load(), results reduced to value array
	 * 
	 * @param LayerAccessArgs $args
	 * @return array
	 */
	public function loadValueList(LayerAccessArgs $args){
		$data = $this->load($args);
		$ValueSource = $this->args->sourceObject();
		return $this->valueList($data, $ValueSource);
	}
	
	/**
	 * Reduce an array of entities to a key=>value array
	 * 
	 * @param array $data Contains entities of type registered in the Source objects
	 * @param ValueSource $KeySource
	 * @param ValueSource $ValueSource
	 */
	public function keyValueList($data, ValueSource $KeySource,	ValueSource$ValueSource) {
		$collection = collection($data);
		$collection->reduce(function($accum, $entity) use ($KeySource, $ValueSource){
			$accum[$KeySource->value($entity)] = $ValueSource->value($entity);
			return $accum;
		}, []);
	}
	
	/**
	 * Reduce an array of entities to a value array
	 * 
	 * @param array $data Contains entities of type registered in the Source object
	 * @param ValueSource $valueSource
	 */
	public function valueList($data, ValueSource $valueSource) {
		$collection = collection($data);
		$collection->reduce(function($accum, $entity) use ($ValueSource){
			$accum[] = $ValueSource->value($entity);
			return $accum;
		}, []);
	}
	
//	public function validateSource($entity, $source) {
//		return $entity->has($source) && method_exists($this->entityClass('namespaced'), $value_source) ;
//	}
//	
//	public function value($entity, $source) {
//		if(in_array($source, $entity->visibleProperties())) {
//			$result = $entity->$source;
//		} else {
//			$result = $entity->$source();
//		}
//		return $result;
//	}
	
	public function filter($argObj) {
		
	}

//	
	public function linkedTo($foreign, $foreign_id, $linked = null){
		
	}
	
	public function IDs($layer = null){
		
	}
	
	/**
	 * Full feature load() with pagination at the end
	 * 
	 * @param LayerAccessArgs $argObj
	 * @return array
	 */
	public function loadPage(LayerAccessArgs $argObj) {
		$data = $this->load($argObj);
		return $this->paginate($data, $argObj);
	}
	/**
	 * Paginate provided array
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

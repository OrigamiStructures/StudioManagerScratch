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
    
    protected $primary;


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
    
	/**
	 * Get the IDs of all the primary entities in the stored stack entities
	 * 
	 * @return array
	 */
	public function members() {
		return array_keys($this->_data);
	}
	
	public function member($id) {
		if (in_array($id, $this->members())) {
			return $this->_data[$id];
		}
		return null;
	}
    
//	public function all($property);
//	
	public function loadDistinct($argObj, $sourcePoint = null){
		if (is_null($sourcePoint)) {
			$ValueSource = $argObj->ValueSource;
		} else {
			$ValueSource = new ValueSource(
					$argObj->valueOf('layer'), 
					$sourcePoint
				);
		}
		$result = $this->load($argObj);
		return $this->trait_distinct($ValueSource, $result);
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
	 * The keys and values may be from properties or from methods on the 
	 * entity. If from a method, that method can have no arguemnts. 
	 * 
	 * @param string|KeySource $keySource Name of (or ValueSource defining) a property or method
	 * @param string|ValueSource $valueSource Name of (or ValueSource defining) a property or method
	 * @param array $data Array of entities
	 * @return array 
	 */
	public function keyValueList($keySource, $valueSource, $data) {
		$KeySource = $this->defineValueSource($keySource, $data);
		$ValueSource = $this->defineValueSource($valueSource, $data);
		if($KeySource && $ValueSource) {
			$result = collection($data)
					->reduce(function($harvest, $entity) use ($KeySource, $ValueSource){
						$harvest[$KeySource->value($entity)] = $ValueSource->value($entity);
						return $harvest;
					}, []);
		} else {
			$result = [];
		}
		return $result;
	}
	
	/**
	 * Reduce an array of entities to a value array
	 * 
	 * The values may be from a property or from a method on the 
	 * entity. If from a method, that method can have no arguemnts. 
	 * 
	 * @param string|ValueSource $sourcePoint Name of (or ValueSource defining) a property or method
	 * @param array $data Array of entities
	 * @return array Array containing the unique values found
	 */
	public function valueList($sourcePoint, $data =[]) {
		$ValueSource = $this->defineValueSource($sourcePoint, $data);
		if ($ValueSource) {
			$result = collection($data)
					->reduce(function ($harvest, $entity) use ($ValueSource){
						if (!is_null($ValueSource->value($entity))) {
							array_push($harvest, $ValueSource->value($entity));
						}
						return $harvest;
					}, []);
		} else {
			$result = [];
		}
		return $result;
	}
	
	/**
	 * Get unique values from a set of entities
	 * 
	 * The values may be from a property or from a method on the 
	 * entity. If from a method, that method can have no arguemnts. 
	 * 
	 * @param string|ValueSource $sourcePoint Name of (or ValueSource defining) a property or method
	 * @param array $data Array of entities
	 * @return array Array containing the unique values found
	 */
	public function trait_distinct($sourcePoint, $data = null) {
		$rawData = $this->insureData($data);
		return array_unique($this->valueList($sourcePoint, $rawData));
	}
	
	private function insureData($data) {
		if ($data !== null) {
			$result = $data;
		} elseif (is_a($this, 'App\Model\Lib\Layer')) {
			$result = $this->_data;
		} else {
			$result = [];
		}
		return $result;
	}
	
	/**
	 * Create the ValueSource object for distinct()
	 * 
	 * @param mixed $sourcePoint
	 * @param array $data
	 * @return boolean|ValueSource
	 */
	private function defineValueSource($sourcePoint, $data) {
		if (empty($data)) {
			return FALSE;
		}
		if (is_a($sourcePoint, 'App\Model\Lib\ValueSource')) {
			return $sourcePoint;
		}
		$entity = array_pop($data);
		return new ValueSource($entity, $sourcePoint);
	}
	
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

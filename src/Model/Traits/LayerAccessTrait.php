<?php
namespace App\Model\Traits;

use App\Model\Lib\LayerAccessArgs;
use App\Model\Lib\Layer;
use App\Model\Lib\ValueSource;

define('LAYERACC_ID', FALSE);
define('LAYERACC_INDEX', TRUE);

define('LAYERACC_LAYER', FALSE);
define('LAYERACC_ARRAY', TRUE);

define('LAYERACC_UNWRAP', TRUE);
define('LAYERACC_WRAP', FALSE);

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
    
	/**
	 * Make an object to set up filtering and access to content
	 * 
	 * Returns a LayerAccessArgs object that allows chained calls 
	 * for object querying. 
	 * 
	 * Passing a $layer value will run the setLayer( ) method 
	 * on the returned Args object
	 * 
	 * @param string $layer 
	 * @return LayerAccessArgs
	 */
    public function find($layer = NULL) {
        $args = new LayerAccessArgs($this);
		if (!is_null($layer)) {
			$args->setLayer($layer);
		}
        return $args;
    }
		
	public function layer(array $entities) {
		return new Layer($entities);
	}
	
	/**
	 * Return the n-th stored element or element(ID)
	 * 
	 * Data is stored in id-indexed arrays, but this method will let you 
	 * pluck the id's or n-th item out
	 * 
	 * @param int $number Array index 0 through n or Id of element
	 * @param boolean $byIndex LAYERACC_INDEX or LAYERACC_ID
	 * @return Entity
	 */
	public function element($key, $byIndex = LAYERACC_INDEX){
		if ($byIndex) {
			$data = array_values($this->load());
			if (count($data) > $key) {
				$result = $data[$key];
			} else {
				$result = null;
			}
		} else {
			if (in_array($key, $this->IDs())) {
				$result = $this->_data[$key];
			} else {
				$result = null;
			}
		}
			return $result;
	}
    
	/**
	 * Get the IDs of all the primary entities in the stored stack entities
	 * 
	 * @return array
	 */
	public function IDs() {
		return array_keys($this->_data);
	}
	
//	public function all($property);
//	
	public function loadDistinct($argObj, $sourcePoint = null){
		if (is_null($sourcePoint)) {
			$ValueSource = $argObj->accessNodeObject('value');
		} else {
			$ValueSource = new ValueSource(
					$argObj->valueOf('layer'), 
					$sourcePoint
				);
		}
		$result = $this->load($argObj);
		return $this->distinct($ValueSource, $result);
	}
	
	/**
	 * Full feature load(), results reduced to key=>value arrry
	 * 
	 * @param LayerAccessArgs $args
	 * @return array
	 */
	public function loadKeyValueList(LayerAccessArgs $args){
		$data = $this->load($args);
		$KeySource = $args->accessNodeObject('key');
		$ValueSource = $args->accessNodeObject('value');
		return $this->keyValueList($KeySource, $ValueSource, $data);
	}
	
	/**
	 * Full feature load(), results reduced to value array
	 * 
	 * @param LayerAccessArgs $args
	 * @return array
	 */
	public function loadValueList(LayerAccessArgs $args){
		$data = $this->load($args);
		$ValueSource = $args->accessNodeObject('value');
		return $this->valueList($ValueSource, $data);
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
	public function keyValueList($keySource, $valueSource, $data = null) {
		$rawData = $this->insureData($data);
		if ($rawData === []) {
			return $rawData;
		}
		$KeySource = $this->defineValueSource($keySource, $rawData);
		$ValueSource = $this->defineValueSource($valueSource, $rawData);
		if($KeySource && $ValueSource) {
			$result = collection($rawData)
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
	 * @param array $data Array of entities (required except for Layer calls)
	 * @return array Array containing the unique values found
	 */
	public function valueList($sourcePoint, $data = null) {
		$rawData = $this->insureData($data);
		if ($rawData === []) {
			return $rawData;
		}
		$ValueSource = $this->defineValueSource($sourcePoint, $rawData);
		if ($ValueSource) {
			$result = collection($rawData)
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
	 * @param array $data Array of entities (required except for Layer calls)
	 * @return array Array containing the unique values found
	 */
	public function distinct($sourcePoint, $data = null) {
		$rawData = $this->insureData($data);
		return array_unique($this->valueList($sourcePoint, $rawData));
	}
	
	/**
	 * Insure some array is passed for methods where the arg is optional
	 * 
	 * Methods that can act on an array allow that arg to be optional 
	 * when it is the last arg so that it doesn't have to be passed 
	 * explicitly in a Layer. But the StackSet and StackEntity require 
	 * it be passed. This insures that when passed, it is used; when 
	 * not passed in a Layer, the layer data is used; and when it 
	 * can't be known, an empty array is used (silent error state)
	 * 
	 * @param null|array $data
	 * @return array
	 */
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

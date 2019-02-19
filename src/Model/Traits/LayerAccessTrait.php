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
	
//	public function load(LayerAccessArgs $argObj);
	
//	public function keyedList($key, $value, $type, $options);
//	
//	public function linkedTo($layer, $id);
}

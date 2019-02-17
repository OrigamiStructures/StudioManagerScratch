<?php
namespace App\Interfaces;

use App\Model\Lib\LayerAccessArgs;
/**
 *
 * @author dondrake
 */
interface LayerAccessInterface {
	
	public function accessArgs();
	
//	public function all($property); //FUTURE FEATURE
	
	public function distinct($propery);
	
	/**
	 * This one seems silly
	 */
//	public function duplicate($property); // FUTURE FEATURE
	
	public function filter($property, $value);
	
	public function load($type, $options);
	
	public function keyedList($key, $value, $type, $options);
	
	public function linkedTo($layer, $id);
	
}

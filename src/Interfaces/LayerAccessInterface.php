<?php
namespace App\Interfaces;

use App\Model\Lib\LayerAccessArgs;
/**
 * Create basic random-access tools for StackSets, StackEntities, and Layers
 * 
 * StackSets, StackEntities, and Layers fit together in a nest of flat 
 * data structures. Stored entities at each level are indexed by their id 
 * so record associations can be reconstructed by using a foreign-key value.
 * 
 * This interface establishes the ways that data can be retrieved, filtered, 
 * formatted, and paginated. Each level of detail will work slightly 
 * differently but from the highest levels it will be possible to reach 
 * down to the very bottom and extract useful data without looping.
 * 
 * 
 *
 * @author dondrake
 */
interface LayerAccessInterface {
	
	/* Done */
	public function accessArgs();
	
	/* Done */
	public function layer(array $entities);
	
	/* Done */
//	public function distinct($propery, $layer = '');
	
	/* Done */
	public function element($number);
	
	/* Done */
	public function IDs($layer = null);
	
	public function keyedList(LayerAccessArgs $argObj);
	
	/* Done */
	public function linkedTo($layer, $id);
	
	/* Done */
	/**
	 * 
	 * @param string|LayerAccessArg $argObj
	 */
	public function load($argObj = null);
	
//	function filter($property, $value);
	
//	public function all($property); //FUTURE FEATURE
	
	
	/**
	 * This one seems silly
	 */
//	public function duplicate($property); // FUTURE FEATURE
	
//	
	
}

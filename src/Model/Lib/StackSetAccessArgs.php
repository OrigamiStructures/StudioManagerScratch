<?php
namespace App\Model\Lib;

use App\Model\Lib\Layer;

/**
 * StackSetAccessArgs
 *
 * @author dondrake
 */
class StackSetAccessArgs extends LayerAccessArgs {
	
	public function loadStacks() {
		
		$layerName = $this->valueOf('layer');
		$result = $this->load(LAYERACC_LAYER);
		$resultIds = $result->IDs();
		
		$stacks = [];
		foreach ($this->data->load() as $stack) {
			$intersection = array_intersect($stack->$layerName->IDs(), $resultIds);
			if (count($intersection) > 0) {
				$stacks[$stack->rootID()] = $stack;
			}
		}
		return $stacks;
		
	}
	
}

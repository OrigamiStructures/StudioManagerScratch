<?php

namespace App\Model\Traits;

/**
 * Description of ReceiverTrait
 *
 * @author dondrake
 */
trait ReceiverTableTrait {
	
	public function initializeReceiverCard() {
	    $this->addLayerTable(['Dispositions']);
		$this->stackSchema[] = ['name' => 'dispositions',	'specs' => ['type' => 'layer']];
		$this->seedPoints = array_merge($this->seedPoints, ['disposition', 'dispositions']);
	}
	
	public function distillFromDisposition($ids) {
		
	}
	
	public function marshalDispositions($id, $stack) {
		return $stack;
	}
	
	
	
}

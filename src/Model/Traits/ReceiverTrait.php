<?php

namespace App\Model\Traits;

/**
 * Description of ReceiverTrait
 *
 * @author dondrake
 */
trait ReceiverTrait {
	
	public function initializeReceiverCard() {
		$this->layerTables[] = 'Dispositons';
		$this->stackSchema[] = ['name' => 'dispositions',	'specs' => ['type' => 'layer']];
		$this->seedPoints = array_merge($this->seedPoints, ['disposition', 'dispositions']);
	}
	
	public function loadFromDisposition($ids) {
		
	}
	
	public function marshalDispositions($id, $stack) {
		return $stack;
	}
	
	
	
}

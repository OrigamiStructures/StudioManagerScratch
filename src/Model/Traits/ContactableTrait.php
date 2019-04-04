<?php

namespace App\Model\Traits;

/**
 * Description of ContactableTrait
 *
 * @author dondrake
 */
trait ContactableTrait {
	
	public function initializeContactableCard() {
		$this->layerTables = array_merge($this->layerTables, ['addresses', 'contacts']);
		$this->stackSchema[] = ['name' => 'addresses',	'specs' => ['type' => 'layer']];
		$this->stackSchema[] = ['name' => 'contacts',	'specs' => ['type' => 'layer']];
		$this->seedPoints = array_merge($this->seedPoints, [
			'address', 
			'addresses', 
			'contact', 
			'contacts'
			]);
	}
	
	public function loadFromAddress($ids) {
		
	}
	
	public function marshalAddresses($id, $stack) {
		return $stack;
	}
	
	public function loadFromContact($ids) {
		
	}
	
	public function marshalContacts($id, $stack) {
		return $stack;
	}
	
}

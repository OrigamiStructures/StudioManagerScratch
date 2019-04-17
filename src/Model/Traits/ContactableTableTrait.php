<?php

namespace App\Model\Traits;

/**
 * Description of ContactableTrait
 *
 * @author dondrake
 */
trait ContactableTableTrait {
	
	public function initializeContactableCard() {
	    $this->addLayerTable(['Addresses', 'Contacts']);
		$this->stackSchema[] = ['name' => 'addresses',	'specs' => ['type' => 'layer']];
		$this->stackSchema[] = ['name' => 'contacts',	'specs' => ['type' => 'layer']];
		$this->seedPoints = array_merge($this->seedPoints, [
			'address', 
			'addresses', 
			'contact', 
			'contacts'
			]);
	}
	
	public function distillFromAddress($ids) {
		$IDs = $this->Addresses->find('list', ['valueField' => 'member_id'])
				->where(['id IN' => $ids]);
		return $this->stacksFromIdentities(array_unique($IDs->toArray()));
	}
	
	public function marshalAddresses($id, $stack) {
		if ($stack->count('identity')) {
			$addresses = $this->Addresses->find('all')
					->where(['member_id' => $id]);
			$stack->set(['addresses' => $addresses->toArray()]);
		}
		return $stack;
	}
	
	public function distillFromContact($ids) {
		$IDs = $this->Contact->find('list', ['valueField' => 'member_id'])
				->where(['id IN' => $ids]);
		return $this->stacksFromIdentities(array_unique($IDs->toArray()));

	}
	
	public function marshalContacts($id, $stack) {
		if ($stack->count('identity')) {
			$contacts = $this->Contacts->find('all')
					->where(['member_id' => $id]);
			$stack->set(['contacts' => $contacts->toArray()]);
		}		
		return $stack;
	}
	
}

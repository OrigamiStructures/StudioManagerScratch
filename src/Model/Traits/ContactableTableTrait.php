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
	    $this->addStackSchema(['addresses', 'contacts']);
		$this->addSeedPoint([
            'addresses',
            'contact', 'contacts'
        ]);
	}
	
	public function distillFromAddress($ids) {
		$IDs = $this->Addresses->find('list', ['valueField' => 'member_id'])
				->where(['id IN' => $ids])
				->toArray();
		return array_unique($IDs);
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
		$IDs = $this->Contacts->find('list', ['valueField' => 'member_id'])
				->where(['id IN' => $ids])
				->toArray();
		return array_unique($IDs);

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

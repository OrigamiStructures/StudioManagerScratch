<?php
namespace App\Model\Traits;

/**
 * Description of ContactableTrait
 *
 * Carries Rolodex heirarchy features for Contact and Address layers
 *
 * @author dondrake
 */
trait ContactableTrait {

	public function hasContacts() {
		return is_a($this->contacts, '\App\Model\Lib\Layer');
	}

	public function contactEntities() {
		if($this->hasContacts()) {
			return $this->contacts->toArray();
		}
		return [];
	}

	public function contactIDs() {
		return $this->valueList('id', $this->contactEntities());
	}


	public function contacts() {
		return $this->valueList('asString', $this->contactEntities());
	}

	public function hasAddresses() {
		return is_a($this->addresses, '\App\Model\Lib\Layer');
	}

	public function addressEntities() {
		if($this->hasAddresses()) {
			return $this->addresses->load();
		}
		return [];
	}

	public function addressIDs() {
		return $this->valueList('id', $this->addressEntities());
	}

	public function addresses() {
		return $this->valueList('asString', $this->addressEntities());
	}
}

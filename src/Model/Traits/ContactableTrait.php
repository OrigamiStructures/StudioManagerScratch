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
		return $this->contacts->IDs();
	}


	public function contacts() {
		return $this->contacts->toValueList('asString');
	}

	public function hasAddresses() {
		return is_a($this->addresses, '\App\Model\Lib\Layer');
	}

	public function addressEntities() {
		if($this->hasAddresses()) {
			return $this->addresses->toArray();
		}
		return [];
	}

	public function addressIDs() {
		return $this->addresses->IDs();
	}

	public function addresses() {
		return $this->addresses->toValueList('asString');
	}
}

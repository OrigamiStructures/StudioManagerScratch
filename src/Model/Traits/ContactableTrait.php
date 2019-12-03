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

    /**
     * @return Layer
     */
	public function getContacts() {
		return $this->contacts;
	}

	public function hasAddresses() {
		return is_a($this->addresses, '\App\Model\Lib\Layer');
	}

    /**
     * @return Layer
     */
	public function getAddresses() {
		return $this->addresses;
	}

}

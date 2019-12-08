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
		return count($this->getContacts()) > 0;
	}

    /**
     * @return Layer
     */
	public function getContacts() {
		return $this->contacts;
	}

	public function hasAddresses() {
		return count($this->getAddresses()) > 0;
	}

    /**
     * @return Layer
     */
	public function getAddresses() {
		return $this->addresses;
	}

}

<?php

namespace App\Model\Entity;

use App\Model\Entity\StackEntity;

/**
 * CakePHP RolodexCard
 * @author dondrake
 */
class RolodexCard extends StackEntity {
	
	protected $_primary = 'identity';

	public function name() {
		return $this->identity->element(0)->name();
	}
	
	public function isMember() {
		return is_a($this->memberships, '\App\Model\Lib\Layer');
	}
	
	public function membershipEntities() {
		if ($this->isMember()) {
			return $this->memberships->load();
		}
		return [];
	}
	
	public function memberships() {
		return $this->distinct('name', $this->membershipEntities());
	}
}

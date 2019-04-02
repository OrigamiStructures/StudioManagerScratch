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
		return $this->primaryEntity()->name();
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
	
	public function membershipIDs() {
		return $this->valueList('id', $this->membershipEntities());
	}
	
	public function memberships() {
		return $this->distinct('name', $this->membershipEntities());
	}
}

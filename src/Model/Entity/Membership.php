<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CakePHP Membership
 * @author dondrake
 */
class Membership extends GroupIdentity {
	
	public function groupId() {
		return $this->group_id;
	}
	
	public function groupIsActive() {
		return $this->group_active;
	}
		
}

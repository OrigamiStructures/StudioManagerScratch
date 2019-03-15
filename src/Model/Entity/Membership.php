<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CakePHP Membership
 * @author dondrake
 */
class Membership extends Entity {
	
	public function id() {
		return $this->id;
	}
	
	public function memberId() {
		return $this->member_id;
	}
	
	public function name() {
		
	}
	
}

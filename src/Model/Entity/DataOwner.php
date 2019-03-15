<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CakePHP DataOwner
 * @author dondrake
 */
class DataOwner extends Entity {
	
	public function id() {
		return $this->id;
	}
	
	public function userId() {
		return $this->username;
	}
	
}

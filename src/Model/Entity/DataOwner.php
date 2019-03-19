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
	
	public function username() {
		return $this->username;
	}
	
	public function ownerOf($param) {
		switch ($param) {
			case is_string($param):
				return $this->id() === $param;
				break;
			case is_object($param):
				return $this->id() === $param->user_id;
				break;
			case is_string($param):
				if (isset($param['user_id'])) {
					return $this->id() === $param;
				}
			default:
				return FALSE;
				break;
		}
	}
	
}

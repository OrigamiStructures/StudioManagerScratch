<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
		return is_a($this->memberships, '\App\Lib\Layer');
	}
	
	public function memberships() {
		if ($this->isMember()) {
			return $this->memberships;
		}
		return [];
	}
}

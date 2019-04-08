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
	
	/**
	 * Is `memberships` a Layer object (TRUE) or array (FALSE)
	 * 
	 * @return boolean
	 */
	public function isMember() {
		return is_a($this->memberships, '\App\Model\Lib\Layer');
	}
	
	public function isGroup() {
		return TRUE;
	}
	
	public function isArtist() {
		return FALSE;
	}
	
	public function isManager() {
		return FALSE;
	}
	
	public function canParticipate() {
		return FALSE;
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

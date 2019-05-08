<?php

namespace App\Model\Entity;

use App\Model\Entity\StackEntity;

/**
 * CakePHP RolodexCard
 * @author dondrake
 */
class RolodexCard extends StackEntity {
	
	/**
	 * @todo Let StackTable::marshalStack() set this
	 * {@inheritdoc}
	 */
	protected $rootName = 'identity';

	/**
	 * @todo Let StackTable::marshalStack() set this
	 * {@inheritdoc}
	 */
	protected $rootDisplaySource = 'name';

	public function name() {
		return $this->rootElement()->name();
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

	/**
	 * Get the membership elements
	 * 
	 * Optionally get the entities as a Layer
	 * 
	 * @param boolean $asLayer 
	 * @return array|layer
	 */
	public function membershipElements($asArray = LAYERACC_ARRAY) {
		if($this->isMember()) {
			$result = $this->memberships->load();
		} else {
			$result = [];
		}
		return $this->_resolveReturnStructure($result, $asArray, 'memberships');
	}
	
	/**
	 * Get the IDs of the Groups this card belongs to
	 * 
	 * @return array
	 */
	public function membershipIDs() {
		return $this->valueList('id', $this->membershipElements());
	}
	
	/**
	 * Get the names of the Groups this card belongs to
	 * 
	 * @return array
	 */
	public function memberships() {
		return $this->distinct('name', $this->membershipElements());
	}
	
}

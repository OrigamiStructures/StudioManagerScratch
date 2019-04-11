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
	
	/**
	 * Get the card identity entity
	 * 
	 * Optionally get the entity as an array element
	 * 
	 * @param boolean $unwrap 
	 * @return entity|array
	 */
	public function identityElement($unwrap = LAYERACC_UNWRAP) {
		$result = $this->identity->load();
		return $this->_resolveWrapper($result, $unwrap);
	}
	
	/**
	 * Get id of the card identity
	 * 
	 * Optionally get the id as an array element
	 * 
	 * @param boolean $unwrap 
	 * @return string|array
	 */
	public function identityID($unwrap = LAYERACC_UNWRAP) {
		$result = $this->identity->IDs();
		return $this->_resolveWrapper($result, $unwrap);
	}
	
	/**
	 * Get name of the card identity
	 * 
	 * Optionally get the name as an array element
	 * 
	 * @param boolean $unwrap 
	 * @return string|array
	 */
	public function identity($unwrap = LAYERACC_UNWRAP) {
		$result = $this->valueList('name', $this->identityElement());
		return $this->_resolveWrapper($result, $unwrap);
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

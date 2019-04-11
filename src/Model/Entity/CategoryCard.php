<?php
namespace App\Model\Entity;

use App\Model\Entity\RolodexCard;

/**
 * Description of CategoryCard
 *
 * @author dondrake
 */
class CategoryCard extends RolodexCard{
	
	public function isGroup() {
		return TRUE;
	}
	
	public function hasMembers() {
		return is_a($this->members, '\App\Model\Lib\Layer');
	}
	
	
	public function memberElements($asArray = LAYERACC_ARRAY) {
		if($this->hasMembers()) {
			$result = $this->members->load();
		} else {
			$result = [];
		}
		return $this->_resolveReturnStructure($result, $asArray, 'member');
	}
	
	public function memberIDs() {
		return $this->valueList('id', $this->memberElements());
	}
	
	public function members() {
		return $this->valueList('name', $this->memberElements());
	}
	
}

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
	
	
	public function memberEntities() {
		if($this->hasMembers()) {
			return $this->members->load();
		}
		return [];
	}
	
	public function memberIDs() {
		return $this->valueList('id', $this->memberEntities());
	}
	
	public function members() {
		return $this->valueList('name', $this->memberEntities());
	}
	
}

<?php
namespace App\Model\Entity;

use App\Model\Entity\RolodexCard;
use App\Model\Lib\Layer;

/**
 * Description of CategoryCard
 *
 * @author dondrake
 * @property Layer $members
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
			$result = $this->members->toArray();
		} else {
			$result = [];
		}
		return $this->_resolveReturnStructure($result, $asArray, 'member');
	}

	public function memberIDs() {
		return $this->valueList('id', $this->memberElements());
	}

	public function members() {
		return $this->members->toValueList('name');
	}

}

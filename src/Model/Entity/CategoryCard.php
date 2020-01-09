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

	public function hasMembers() {
		return count($this->getMembers()) > 0;
	}


	public function getMembers() {
		return $this->members;
	}

}

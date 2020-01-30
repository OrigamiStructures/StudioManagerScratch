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

    /**
     * Get member_ids of managers allowed to see this category
     *
     * @return array
     */
    public function getPermittedManagers()
    {
        return $this->getLayer('shares')
            ->find()
            ->specifyFilter('category_id', $this->rootID())
            ->toValueList('manager_id');
    }

}

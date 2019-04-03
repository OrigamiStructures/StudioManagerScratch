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
	
}

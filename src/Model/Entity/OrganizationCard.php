<?php
namespace App\Model\Entity;

use App\Model\Entity\CategoryCard;
use App\Model\Traits\ContactableTrait;
use App\Model\Traits\ReceiverTrait;

/**
 * Description of CategoryCard
 *
 * @author dondrake
 */
class OrganizationCard extends CategoryCard{
	
	use ContactableTrait, ReceiverTrait;
	
}

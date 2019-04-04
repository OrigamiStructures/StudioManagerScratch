<?php
namespace App\Model\Entity;

use App\Model\Entity\RolodexCard;
use App\Model\Traits\ContactableTrait;
use App\Model\Traits\ReceiverTrait;

/**
 * Description of PersonCard
 *
 * @author dondrake
 */
class PersonCard extends RolodexCard{
	
	use ContactableTrait, ReceiverTrait;

}

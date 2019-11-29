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

    public function registeredUserId()
    {
        /** @var Identity $entity */
        $entity = $this->identity->shift();
        return $entity->registeredUserId();
	}

    public function emitFormData()
    {
        $data = $this->rootElement();
        $data->user = $this->data_owner->shift();
        $data->memberships = $this->getMemberships();
        $data->addresses = $this->addressEntities();
        $data->contacts = $this->contactEntities();
        return $data;
    }

}

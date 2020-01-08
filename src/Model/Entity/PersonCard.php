<?php
namespace App\Model\Entity;

use App\Model\Entity\RolodexCard;
use App\Model\Lib\Layer;
use App\Model\Traits\ContactableTrait;
use App\Model\Traits\ReceiverTrait;

/**
 * Description of PersonCard
 *
 * @author dondrake
 *
 */
class PersonCard extends RolodexCard{

	use ContactableTrait, ReceiverTrait;

    /**
     * The manifests naming this user as manager (issued by foriegn supervisor)
     *
     * calculation requires logged-in user id as param
     *
     * @var null|array
     */
	private $receivedMangement = null;

    /**
     * The manifests delegated to foriegn managers (issued by this supervisor)
     *
     * calculation requires logged-in user id (supervisor_id) as param
     *
     * @var null|array
     */
	private $delegatedManagement = null;

    /**
     * @return int
     */
    public function registeredUserId()
    {
        /** @var Identity $entity */
        $entity = $this->identity->shift();
        return $entity->registeredUserId();

	}

    /**
     * @return bool
     */
    public function isArtist()
    {
        return in_array(
            $this->rootID(),
            $this->getManifests()->toDistinctList('member_id'));
    }

    /**
     * Are there Manifests naming this Member as manager?
     *
     * @return bool
     */
    public function isManager()
    {
        return in_array(
            $this->rootID(),
            $this->getManifests()->toDistinctList('manager_member'));
    }

    /**
     * Are there Manifests where foreign supervisors name this Member as manager?
     *
     * Qualifying Manifests will all have
     *  - manager_member ==   PersonCard::rootID()
     *  - manager_id     ==   $actingUserId
     *  - supervisor_id  !=   $actingUserId
     *
     * The Manifest will belong to the foreign supervisor
     * The named artist will belong to the foreign supervisor
     * This card will belong to $actingUserId
     *
     * Sets a property which will be used in future calls
     *
     * @param $actingUserId string Can be provided by ContextUser::supervisorId()
     * @return bool
     */
    public function isRecievingManager($actingUserId)
    {
        $received = [];
        if ($this->isManager()) {
            $received = $this->receivedMangement ?? $this->receivedManagement($actingUserId);
        }
        return count($received) > 0;
    }

    /**
     * Get the Manifests where foreign supervisors name this Member as manager
     *
     * Returned Manifests will all have
     *  - manager_member ==   PersonCard::rootID()
     *  - manager_id     ==   $actingUserId
     *  - supervisor_id  !=   $actingUserId
     *
     * The Manifest will belong to the foreign supervisor
     * The named artist will belong to the foreign supervisor
     * This card will belong to $actingUserId
     *
     * Sets a property which will be used in future calls
     *
     * @param $actingUserId string Can be provided by ContextUser::supervisorId()
     * @return array
     */
    public function receivedManagement($actingUserId)
    {
        $received = $this->receivedMangement ?? [];
        if ($this->isManager() && count($received) == 0) {
            $manifests = collection($this->getManifests()->toArray());
            $received = $manifests->filter(function($manifest, $key) use ($actingUserId) {
                /* @var Manifest $manifest */
                return $manifest->getOwnerId('manager') == $actingUserId
                    && $manifest->getOwnerId('supervisor') != $actingUserId;
            })
            ->toArray();
        }
        $this->receivedMangement = $received;
        return $received;
    }

    /**
     * Are there Manifests where this supervisor named this foreign Member as manager?
     *
     * Qualifying Manifests will all have
     *  - manager_member ==   PersonCard::rootID()
     *  - manager_id     !=   $actingUserId
     *  - supervisor_id  ==   $actingUserId
     *
     * The Manifest will belong to a this actingUser/supervisor
     * The named artist will belong to the actingUser/supervisor
     * This member card will belong to a foreign user
     *
     * Sets a property which will be used in future calls
     *
     * @param $actingUserId string Can be provided by ContextUser::supervisorId()
     * @return bool
     */
    public function isManagementDelegate($actingUserId)
    {
        $delegates = [];
        if ($this->isManager()) {
            $delegates = $this->delegatedManagement ?? $this->delegatedManagement($actingUserId);
        }
        return count($delegates) > 0;
    }

    /**
     * Get the Manifests where this supervisor names this foreign Member as manager
     *
     *  - manager_member ==   PersonCard::rootID()
     *  - manager_id     !=   $actingUserId
     *  - supervisor_id  ==   $actingUserId
     *
     * The Manifest will belong to a this actingUser/supervisor
     * The named artist will belong to the actingUser/supervisor
     * This card will belong to a foreign user
     *
     * Sets a property which will be used in future calls
     *
     * @param $actingUserId string Can be provided by ContextUser::supervisorId()
     * @return array
     */
    public function delegatedManagement($actingUserId)
    {
        $delegates = $this->delegatedManagement ?? [];
        if ($this->isManager() && count($delegates) == 0) {
            $manifests = collection($this->getManifests()->toArray());
            $delegates = $manifests->filter(function($manifest, $key) use ($actingUserId) {
                /* @var Manifest $manifest */
                return $manifest->getOwnerId('manager') != $actingUserId
                    && $manifest->getOwnerId('supervisor') == $actingUserId;
            })
                ->toArray();
        }
        $this->delegatedManagement = $delegates;
        return $delegates;
    }

    /**
     * @todo proposed method, not tested
     *
     * @param $supervisorId
     * @param bool $distinct
     * @return array
     */
    public function managerDelegateNames($supervisorId, $distinct = TRUE)
    {
        $manifests = collection($this->delegatedManagement($supervisorId));
        $names = $manifests->reduce(function($accum, $manifest) {
            /* @var Manifest $manifest */
            $accum[] = $manifest->getName('manager');
            return $accum;
        }, []);

        if ($distinct) {
            $names = array_unique($names);
        }
        return $names;
    }

    /**
     * @return bool
     */
    public function isSupervisor()
    {
        return in_array(
            $this->rootID(),
            $this->getManifests()->toDistinctList('supervisor_member'));
    }

    /**
     * @return Member
     */
    public function emitFormData()
    {
        $data = $this->rootElement();
        $data->user = $this->data_owner->shift();
        $data->memberships = $this->getMemberships()->toArray();
        $data->addresses = $this->getAddresses()->toArray();
        $data->contacts = $this->getContacts()->toArray();
        return $data;
    }

    /**
     * Convenience alternative for getLayer('images') in PersonCards
     *
     * @return Layer
     */
    public function getImages()
    {
        return $this->images;
    }
    /**
     * Convenience alternative for getLayer('manifests') in PersonCards
     *
     * @return Layer
     */
    public function getManifests()
    {
        return $this->manifests;
    }

    /**
     * @return bool
     */
    public function hasManifests()
    {
        return count($this->getManifests()) > 0;
    }

}

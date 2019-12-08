<?php

namespace App\Model\Entity;

use App\Model\Entity\StackEntity;
use App\Model\Lib\Layer;

/**
 * CakePHP RolodexCard
 * @author dondrake
 * @property Layer $memberships
 * @property Layer $identity
 * @property Layer $data_owner
 */
class RolodexCard extends StackEntity {

	/**
	 * @todo Let StackTable::marshalStack() set this
	 * {@inheritdoc}
	 */
	protected $rootName = 'identity';

	/**
	 * @todo Let StackTable::marshalStack() set this
	 * {@inheritdoc}
	 */
	public $rootDisplaySource = 'name';

    /**
     * @return array|Layer
     */
    public function getIdentity() {
        return $this->identity;
    }

    /**
     * @return array|Layer
     */
    public function getDataOwner() {
        return $this->data_owner;
    }

    /**
     * @return mixed
     */
    public function name() {
		return $this->rootElement()->name();
	}

	/**
	 * does `memberships` layer have elements
	 *
	 * @return boolean
	 */
	public function isMember() {
		return $this->memberships->count() > 0;
	}

    /**
     * @return mixed
     */
    public function isGroup() {
	    return $this->rootElement()->isGroup();
	}

    /**
     * @return bool
     */
    public function isArtist() {
		return FALSE;
	}

    /**
     * @return bool
     */
    public function isManager() {
        return FALSE;
    }

    /**
     * @return bool
     */
    public function isSupervisor() {
        return FALSE;
    }

    /**
     * @return bool
     */
    public function canParticipate() {
		return FALSE;
	}

	/**
	 * Get the membership elements
	 *
	 * Optionally get the entities as a Layer
	 *
	 * @return Layer
	 */
	public function getMemberships() {
	    return $this->memberships;
	}

}

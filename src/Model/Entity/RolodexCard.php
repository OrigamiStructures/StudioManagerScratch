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
 * @method Member rootElement($unwrap = LAYERACC_UNWRAP)
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
    public function name($style = null) {
		return $this->rootElement()->name($style);
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

    public function isOrganization() {
        return $this->rootElement()->isOrganization();
    }

    public function isCategory() {
        return $this->rootElement()->isCategory();
    }

    public function isPerson() {
        return $this->rootElement()->isPerson();
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

    /**
     * Get member_ids of managers allowed to see this category
     *
     * @return array
     */
    public function getPermittedManagers()
    {
        return [];
    }

    /**
     * Get member_ids of categories this manager is allowedd to see
     *
     * @return array
     */
    public function getPermittedCategories()
    {
        return [];
    }

    /**
     * Get member_id of categories this supervisor is sharing with managers
     *
     * @return array
     */
    public function getShareCategories()
    {
        return [];
    }

}

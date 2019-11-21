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
    public function getIdentity($asArray = LAYERACC_ARRAY) {
        if($this->identity->count() > 0) {
            $result = $this->identity->toarray();
        } else {
            $result = [];
        }
        return $this->_resolveReturnStructure($result, $asArray, 'identity');
    }

    /**
     * @return array|Layer
     */
    public function getDataOwner($asArray = LAYERACC_ARRAY) {
        if($this->data_owner->count() > 0) {
            $result = $this->data_owner->toarray();
        } else {
            $result = [];
        }
        return $this->_resolveReturnStructure($result, $asArray, 'data_owner');
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
    public function canParticipate() {
		return FALSE;
	}

	/**
	 * Get the membership elements
	 *
	 * Optionally get the entities as a Layer
	 *
	 * @param boolean $asLayer
	 * @return array|layer
	 */
	public function getMemberships($asArray = LAYERACC_ARRAY) {
		if($this->isMember()) {
			$result = $this->memberships->toarray();
		} else {
			$result = [];
		}
		return $this->_resolveReturnStructure($result, $asArray, 'memberships');
	}

	/**
	 * Get the IDs of the Groups this card belongs to
	 *
	 * @return array
	 */
	public function membershipIDs() {
		return $this->memberships->toValueList('id');
	}

	/**
	 * Get the names of the Groups this card belongs to
	 *
	 * @return array
	 */
	public function memberships() {
		return $this->memberships->toDistinctList('name');
	}

}

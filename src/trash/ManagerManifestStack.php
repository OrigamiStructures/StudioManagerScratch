<?php
namespace App\Model\Entity;

use App\Model\Entity\StackEntity;
use App\Model\Lib\StackSet;

/**
 * ManifestStackEntity
 *
 *  * There are multiple PersonCards required to identify everyone included
 * in a Manifest. But they may all be the same person. So, they are stored
 * in a pool, rather than seperately. So, if only one is needed, we only
 * have one, if 3 are needed we have them all.
 *
 * @author dondrake
 *
 * @property StackSet $people
 * @property Layer $manifest
 */
class ManagerManifestStack extends StackEntity {

	public function manifest() {
//	    return $this->manifest->element(0, LAYERACC_INDEX);
		return $this->manifest->shift();
	}

    /**
     * Get the Person Card describing this Supervisor
     *
     * @return PersonCard
     */
	public function supervisorCard() {
		$id = $this->rootElement()->supervisorId();
        $identityIDs = $this->people
            ->getLayer('identity')
            ->find()
            ->specifyFilter('supervisor_id', $id)
            ->toDistinctList('id');
        $card = $this->people->stacksContaining('identity', $identityIDs);
		return array_pop($card);
	}

    /**
     * Get the Person Card describing this Manager
     *
     * @return PersonCard
     */
	public function managerCard() {
		$id = $this->rootElement()->managerId();
		$identityIDs = $this->people
            ->getLayer('identity')
            ->find()
            ->specifyFilter('manager_id', $id)
            ->toDistinctList('id');
        $card = $this->people->stacksContaining('identity', $identityIDs);
        return array_pop($card);
	}

    public function selfAssigned()
    {
        return $this->rootElement()->selfAssigned();
	}

    public function accessSummary()
    {
        return (!isset($this->permissions) || $this->permissions->count() == 0)
		? "Full Access"
		: "Limited Access";
	}

}
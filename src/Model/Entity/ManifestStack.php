<?php
namespace App\Model\Entity;

use App\Model\Entity\StackEntity;
use App\Model\Lib\StackSet;

/**
<<<<<<< Updated upstream
 * ManifestStackEntity
=======
 * StackEntity
>>>>>>> Stashed changes
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
class ManifestStack extends StackEntity {

	public function manifest() {
//	    return $this->manifest->element(0, LAYERACC_INDEX);
		return $this->manifest->shift();
	}

    /**
     * Get the PersonCard describing this Artist
     *
     * @return PersonCard
     */
    public function artistCard() {
        $id = $this->rootElement()->getMemberId('artist');

        $identityIds = $this->people
            ->getLayer('identity')
            ->find()
            ->specifyFilter('id', $id)
            ->toDistinctList('id');

        $card = $this->people->stacksContaining('identity', $identityIds);

        return array_pop($card);
    }

    /**
     * Get the Person Card describing this Supervisor
     *
     * @return PersonCard
     */
	public function supervisorCard() {
		$id = $this->rootElement()->getOwnerId('supervisor');
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
		$id = $this->rootElement()->getOwnerId('manager');
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
        return $this->rootElement()->isSelfAssigned();
	}

    public function accessSummary()
    {
        return (!isset($this->permissions) || $this->permissions->count() == 0)
		? "Full Access"
		: "Limited Access";
	}

}

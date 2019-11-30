<?php
namespace App\Model\Entity;

use App\Model\Entity\ManagerManifestStack;

/**
 * ArtistManifestStack
 *
 * There are multiple PersonCards required to identify everyone included
 * in a Manifest. But they may all be the same person. So, they are stored
 * in a pool, rather than seperately. So, if only one is needed, we only
 * have one, if 3 are needed we have them all.
 *
 * @author dondrake
 */
class ArtistManifestStack extends ManagerManifestStack {

    /**
     * Get the PersonCard describing this Artist
     *
     * @return PersonCard
     */
	public function artistCard() {
		$id = $this->rootElement()->artistId();

		$identityIds = $this->people
            ->getLayer('identity')
            ->find()
            ->specifyFilter('id', $id)
            ->toDistinctList('id');

		$card = $this->people->stacksContaining('identity', $identityIds);

		return array_pop($card);
	}
}

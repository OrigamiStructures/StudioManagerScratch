<?php
namespace App\Model\Entity;

use App\Model\Entity\ManagerManifestStack;

/**
 * CakePHP ManifestStackEntity
 * @author dondrake
 */
class ArtistManifestStack extends ManagerManifestStack {
	
	public function artistCard() {
		$id = $this->rootElement()->artistId();
		$card = $this->people
				->find('identity')
				->specifyFilter('id', $id)
				->loadStacks();
		return array_pop($card);
	}
}

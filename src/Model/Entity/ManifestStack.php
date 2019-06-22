<?php
namespace App\Model\Entity;

use App\Model\Entity\StackEntity;

/**
 * CakePHP ManifestStackEntity
 * @author dondrake
 */
class ManifestStack extends StackEntity {
	
	public function supervisorCard() {
		$id = $this->rootElement()->supervisorId();
		$card = $this->people
				->find('identity')
				->specifyFilter('supervisor_id', $id)
				->loadStacks();
		return array_pop($card);
	}
	
	public function managerCard() {
		$id = $this->rootElement()->managerId();
		$card = $this->people
				->find('identity')
				->specifyFilter('manager_id', $id)
				->loadStacks();
		return array_pop($card);
	}
	
	public function artistCard() {
		$id = $this->rootElement()->artistId();
		$card = $this->people
				->find('identity')
				->specifyFilter('id', $id)
				->loadStacks();
		return array_pop($card);
	}
	
}

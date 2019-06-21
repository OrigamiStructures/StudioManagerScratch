<?php
namespace App\Model\Entity;

use App\Model\Entity\StackEntity;

/**
 * CakePHP ManifestStackEntity
 * @author dondrake
 */
class ManifestStack extends StackEntity {
	
	public function supervisorCard() {
		$this->rootElement()->supervisorId();
	}
	
	public function managerCard() {
		$this->rootElement()->managerId();
	}
	
	public function artistCard() {
		$this->rootElement()->artistId();
	}
	
}

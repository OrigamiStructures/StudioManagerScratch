<?php
namespace App\Model\Traits;

use Cake\ORM\TableRegistry;

/**
 * ManagementTrait
 * 
 * Provide supervisor/manager manifests for classes that need to 
 * work with shared data. This trait can operate in Controller or 
 * Table environments
 *
 * @author dondrake
 */
trait ManagementTrait {
	
	protected $supervision;

	protected $management;
	
	public function setSupervisorManifests($id) {
		$this->supervision = $this->manifests('supervisor', $id);
		return $this->supervision;
	}
	
	public function setManagerManifests($id) {
		$this->management = $this->manifests('manager', $id);
		return $this->management;
	}
	
	public function supervision() {
		return $this->supervision;
	}
	
	public function management() {
		return $this->management;
	}

	protected function ManifestTable() {
		return TableRegistry::getTableLocator()->get('ArtistManifestStacks');
	}
	
	protected function manifests($seed, $id) {
		return $this->ManifestTable()
				->find('stacksFor', ['seed' => $seed, 'ids' => [$id]]);
	}
	
}

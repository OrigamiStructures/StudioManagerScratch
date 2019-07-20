<?php
namespace App\Model\Entity;

use App\Model\Entity\StackEntity;

/**
 * CakePHP ManifestStackEntity
 * @author dondrake
 */
class ManagerManifestStack extends StackEntity {

    /**
     * @var \App\Model\Lib\StackSet
     */
    protected $people;

    /**
     * @var \App\Model\Lib\Layer
     */
    protected $manifest;
	
	public function manifest() {
		return $this->manifest->shift();
	}
	
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

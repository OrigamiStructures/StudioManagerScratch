<?php


namespace App\Controller;


use Cake\ORM\TableRegistry;

class SupervisorsController extends AppController
{
    public function index()
    {
        $currentUser = $this->currentUser();
        $ManifestStacks = TableRegistry::getTableLocator()->get('ManifestStacks');
        $supervisorManifests = 
				$ManifestStacks
				->find('supervisorManifests', ['source' => 'currentUser']);
        $managerManifests = $ManifestStacks->find('stacksFor', ['seed' => 'manager', 'ids' => [$currentUser->managerId()]]);
        $this->set(compact(['supervisorManifests','managerManifests','currentUser']));
		
		$filter = $supervisorManifests
				->find('manifest')
				->specifyFilter('selfAssigned', FALSE)
				->loadStacks();
    }

	public function manager() {
		osd($this->request->data,'Gather the manifests for this manager');die;
		$this->redirect('/supervisors/index');
	}

	public function artist() {
		osd($this->request->data, 'Gather the manifests for this artist');die;
		$this->redirect('/supervisors/index');
	}
	
}
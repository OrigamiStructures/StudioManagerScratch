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
		
        $managerManifests = 
				$ManifestStacks
				->find('managerManifests', ['source' => 'currentUser']);
		
        $this->set(compact(['supervisorManifests','managerManifests','currentUser']));
    }

	public function manager() {
        $ManifestStacks = TableRegistry::getTableLocator()->get('ManifestStacks');
		osd($this->request->data('assignments'));//die;
		$managerManifests =
				$ManifestStacks
				->find('managerManifests', ['ids' => [$this->request->data('assignments')]]);
        $this->set(compact(['managerManifests']));
//		osd($this->request->data,'Gather the manifests for this manager');die;
//		$this->redirect('/supervisors/index');
	}

	public function artist() {
		osd($this->request->data, 'Gather the manifests for this artist');die;
		$this->redirect('/supervisors/index');
	}
	
}
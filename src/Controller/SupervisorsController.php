<?php


namespace App\Controller;


use Cake\ORM\TableRegistry;

class SupervisorsController extends AppController
{
    public function index()
    {
        $currentUser = $this->currentUser();
        $ManifestStacks = TableRegistry::getTableLocator()->get('ArtistManifestStacks');
        $PersonCards = TableRegistry::getTableLocator()->get('PersonCards');
		
        $supervisorManifests = 
				$ManifestStacks
				->find('supervisorManifests', ['source' => 'currentUser']);
		
        $managerManifests = 
				$ManifestStacks
				->find('managerManifests', ['source' => 'currentUser']);

        $myPersonCards =
                $PersonCards
                ->find('stacksFor', ['seed' => 'data_owner', 'ids' => [$this->currentUser()->userId()]]);

		
        $this->set(compact(['supervisorManifests','managerManifests','currentUser', 'myPersonCards']));
    }

	public function manager() {
        $ManifestStacks = TableRegistry::getTableLocator()->get('ArtistManifestStacks');
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

    public function createArtist()
    {
        if (is_null($this->request->data('artistId')))
        {
            //setup some information
            //render create page in AddressBook

        }
        else
            {
                $artistId = $this->request->data('artistId');
            $this->createArtistManifest($artistId);
        }

	}
	
}
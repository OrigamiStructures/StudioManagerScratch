<?php


namespace App\Controller;


use Cake\ORM\TableRegistry;
use App\Model\Lib\ContextUser;

class SupervisorsController extends AppController
{
    public function index()
    {
        $contextUser = $this->contextUser();

        $ManagerManifestStacks = TableRegistry::getTableLocator()->get('ManagerManifestStacks');
        $ArtistManifestStacks = TableRegistry::getTableLocator()->get('ArtistManifestStacks');
        $PersonCards = TableRegistry::getTableLocator()->get('PersonCards');

        $managerManifests = $ManagerManifestStacks->find('supervisorManifests');

        $managementAgreements = $ArtistManifestStacks->find('supervisorManifests');

        $myPersonCards =
                $PersonCards
                ->find('stacksFor', ['seed' => 'data_owner', 'ids' => [$contextUser->getId('supervisor')]]);
//                ->find('stacksFor', ['seed' => 'data_owner', 'ids' => [$this->contextUser()->userId()]]);


        $this->set(compact(['managementAgreements','managerManifests','contextUser', 'myPersonCards']));
    }

	public function manager() {
        $ManifestStacks = TableRegistry::getTableLocator()->get('ArtistManifestStacks');
		osd($this->request->data('assignments'));//die;
		$managerManifests =
				$ManifestStacks
				->find('managerManifests', ['ids' => [$this->request->getData('assignments')]]);
        $this->set(compact(['managerManifests']));
//		osd($this->request->data,'Gather the manifests for this manager');die;
//		$this->redirect('/supervisors/index');
	}

	public function artist() {
		osd($this->request->getData(), 'Gather the manifests for this artist');//die;
		$this->redirect('/supervisors/index');
	}

    public function createArtist()
    {
        if (is_null($this->request->getData('artistId')))
        {
            //setup some information
            //render create page in AddressBook

        }
        else
            {
                $artistId = $this->request->getData('artistId');
            $this->createArtistManifest($artistId);
        }

	}

}

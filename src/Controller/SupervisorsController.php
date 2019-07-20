<?php


namespace App\Controller;


use Cake\ORM\TableRegistry;
use App\Model\Lib\ContextUser;

class SupervisorsController extends AppController
{
    public function index()
    {
        $contextUser = $this->contextUser();
		$s = new \Cake\Http\Session();
		$s->delete('f22f9b46-345f-4c6f-9637-060ceacb21b2');
		debug(ContextUser::instance());
		
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
				->find('managerManifests', ['ids' => [$this->request->data('assignments')]]);
        $this->set(compact(['managerManifests']));
//		osd($this->request->data,'Gather the manifests for this manager');die;
//		$this->redirect('/supervisors/index');
	}

	public function artist() {
		osd($this->request->data, 'Gather the manifests for this artist');//die;
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
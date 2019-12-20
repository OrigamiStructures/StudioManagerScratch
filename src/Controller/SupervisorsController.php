<?php


namespace App\Controller;


use App\Model\Table\ManifestsTable;
use App\Model\Table\ManifestStacksTable;
use Cake\ORM\TableRegistry;
use App\Model\Lib\ContextUser;

class SupervisorsController extends AppController
{
    public function index()
    {
        /* @var ManifestStacksTable $ManifestStacks */
        $contextUser = $this->contextUser();

        $ManifestStacks = TableRegistry::getTableLocator()->get('ManifestStacks');
        $PersonCards = TableRegistry::getTableLocator()->get('PersonCards');

        $manifestsIssued = $ManifestStacks->ManifestsIssued();

        $manifestsReceived = $ManifestStacks->ManifestsRecieved();

        $myPersonCards =
                $PersonCards
                ->find('stacksFor', ['seed' => 'data_owner', 'ids' => [$contextUser->getId('supervisor')]]);


        $this->set(compact(['manifestsIssued', 'manifestsReceived','contextUser', 'myPersonCards']));
    }

	public function manager() {
        $ManifestStacks = TableRegistry::getTableLocator()->get('ManifestStacks');
		osd($this->request->getData('assignments'));//die;
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

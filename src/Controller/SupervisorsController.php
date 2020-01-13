<?php


namespace App\Controller;


use App\Exception\IllegalAccessException;
use App\Model\Table\ManifestsTable;
use App\Model\Table\ManifestStacksTable;
use App\View\Helper\CardFileHelper;
use Cake\ORM\TableRegistry;
use App\Model\Lib\ContextUser;

/**
 * Class SupervisorsController
 * @package App\Controller
 *
 * @property CardFileHelper $CardFile
 */
class SupervisorsController extends AppController
{
    public function index()
    {
        /* @var ManifestStacksTable $ManifestStacks */
        $contextUser = $this->contextUser();
        $supervisor_id = $contextUser->getId('supervisor');
        $ManifestStacks = TableRegistry::getTableLocator()->get('ManifestStacks');
        $PersonCards = TableRegistry::getTableLocator()->get('PersonCards');

        $manifestsIssued = $ManifestStacks->ManifestsIssued($supervisor_id);

        $manifestsReceived = $ManifestStacks->ManifestsRecieved($supervisor_id);

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

    public function admin()
    {
        if (!$this->contextUser()->isSuperuser()) {
            $msg = 'Insufficient permissions to view this page';
            throw new IllegalAccessException($msg);
        }

        //display subset of Supervisors, Managers, or Artists
        //to allow the superuser to act-as for testing and customer support

    }

    public function permissions($manifest_id)
    {
        $ManifestStacksTable = TableRegistry::getTableLocator()->get('ManifestStacks');
        /* @var ManifestStacksTable $ManifestStacksTable */

        $manifestStack = $ManifestStacksTable->stacksfor('manifest', [$manifest_id])->shift();
        $referer = $this->referer();
        $this->set(compact('manifestStack', 'referer'));
    }

    public function actAs($role, $id)
    {
        if (!$this->contextUser()->isSuperuser()) {
            $msg = 'Insufficient permissions to view this page';
            throw new IllegalAccessException($msg);
        }

        switch ($role) {
            case 'supervisor':
                $this->contextUser()->set($role, $id);
                return $this->redirect([
                    'controller' => 'cardfile',
                    'action' => 'view',
                    $this->contextUser()->getCard('supervisor')->rootID()
                ]);
                break;
            default:
                break;
        }

    }
}

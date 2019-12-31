<?php
namespace App\Controller;

use App\Controller\Component\PreferencesComponent;
use App\Form\LocalPreferencesForm as LocalPrefsForm;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use App\Model\Table\MembersTable;
use Cake\Utility\Hash;

/**
 * Class AddressBookController
 * @package App\Controller\
 *
 * @property MembersTable $Members
 * @property PreferencesComponent $Preferences
 */
class AddressBookController extends AppController
{
    public $paginate = [
        'limit' => 5,
		'sort' => 'last_name',
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Preferences');
    }

    public function index()
    {
        $PersonCards = TableRegistry::getTableLocator()->get('PersonCards');
        $ids = $PersonCards->Identities->find('list')
            ->order(['last_name'])
            ->toArray();

        $prefsForm = new LocalPrefsForm();
        $prefs = $prefsForm->getUserPrefs($this->contextUser()->getId('supervisor'));
        $people = $this->paginate($PersonCards->pageFor('identity', $ids));
//        $prefsForm->asContext(Hash::flatten($prefs->prefs));

        $this->set(compact('people', 'prefs', 'prefsForm'));
    }

    public function setPref()
    {
        $prefsForm = new LocalPrefsForm();
//        osd($this->getRequest());die;
        if ($this->getRequest()->is('post') || $this->getRequest()->is('put')) {
            $prefsForm->validate($this->getRequest()->getData());
            osd($prefsForm->getErrors());
            die;
        }

        $this->Preferences->setPref();
    }

    /**
     * View method
     *
     * @param string|null $id Member id.
     * @return void
     * @throws NotFoundException When record not found.
     */
    public function view($id = null)     {
        try {
            $member = $this->Members->get($id, [
                'contain' => ['Images', 'Groups', 'Users', 'Dispositions', 'Locations']
            ]);
        } catch (\Exception $e) {
            //No record has been found
            $this->set('error_message', $e->getMessage());
        }
        $this->set('member', $member);
        $this->set('_serialize', ['member']);
    }


}

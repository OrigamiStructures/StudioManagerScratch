<?php
namespace App\Controller;

use App\Controller\Component\PreferencesComponent;
use App\Exception\BadPrefsImplementationException;
use App\Form\LocalPreferencesForm as LocalPrefsForm;
use App\Form\PrefCon;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use App\Model\Table\MembersTable;
use Cake\Utility\Hash;
use http\Exception\BadMethodCallException;

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
        $prefs = $prefsForm->getUsersPrefsEntity($this->contextUser()->getId('supervisor'));
        $people = $this->paginate(
            $PersonCards->pageFor('identity', $ids),
            [
                'limit' => $prefs->for(PrefCon::PAGINATION_LIMIT),
                'sort' => $prefs->for(PrefCon::PAGINATION_SORT_PEOPLE)
            ]
        );

//        $prefsForm->setErrors([
//            'pagination' => ['sort' => ['people' => [
//                'inList' => 'Sorting can only be done on first_name or last_name'
//            ]]]
//        ]);

        $this->set(compact('people', 'prefs', 'prefsForm'));
    }

    /**
     * This will not be accessible for the API
     */
    public function setPrefs()
    {
        if (!$this->getRequest()->is('post') && !$this->getRequest()->is('put'))
        {
            $msg = __("Preferences can only be changed through POST or PUT");
            throw new BadPrefsImplementationException($msg);
        }

        $this->Preferences->setPrefs();

        /*
         * see https://github.com/OrigamiStructures/StudioManagerScratch/issues/172
         */
//        if (empty($prefsForm->getErrors())) {
            return $this->redirect($this->referer());
//        }

//        $this->set(compact('prefsForm', 'prefs'));
//        $this->render('/UserPrefs/set_prefs');
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

<?php
namespace App\Controller;

use App\Controller\Component\PreferencesComponent;
use App\Exception\UnknownMemberTypeException;
use App\Constants\MemCon;
use App\Constants\PrefCon;
use App\Form\CardfileFilter;
use App\Lib\Wildcard;
use App\Model\Entity\Member;
use App\Model\Entity\RolodexCard;
use App\Model\Entity\Manifest;
use App\Model\Entity\Share;
use App\Model\Lib\Layer;
use App\Model\Table\ArtStacksTable;
use App\Model\Table\CategoryCardsTable;
use App\Model\Table\IdentitiesTable;
use App\Model\Table\ManifestsTable;
use App\Model\Table\PersonCardsTable;
use App\Model\Table\RolodexCardsTable;
use App\Model\Table\UsersTable;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use App\Model\Entity\PersonCard;
use App\Model\Lib\StackSet;
use Cake\View\ViewBuilder;
use Cake\Http\Exception\NotFoundException;

/**
 * CakePHP CardFileController
 *
 * Card File is the central controller for managing and viewing
 * the diffent kinds of Cards (Rolodex Cards).
 *
 * The index pages respond to search, and pagination honors the
 * search requests. Search filters effect only the index page they
 * are created on. Search filters survive veiw/xx visits and will
 * still apply when the user returs to the originating index page.
 *
 * @author dondrake
 * @property PersonCard $PersonCard
 * @property RolodexCard $RolodexCard
 * @property RolodexCardsTable $RolodexCards
 * @property PreferencesComponent $Preferences
 */
class CardFileController extends AppController {

    /**
     * Pagination Component defaults
     *
     * @var array
     */
    public $paginate = [
        'limit' => 20,
    ];

    public $components = ['Preferences'];

    public $name = 'CardFile';

    /**
     * @var bool|IdentitiesTable
     */
    protected $Identities = false;

    /**
     * @var bool|PersonCardsTable
     */
    protected $PersonCards = false;

    /**
     * Set up the CardFile Controller
     */
    public function initialize() {
        parent::initialize();
    }

    //<editor-fold desc="********** CRUD Methods">

    public function insert($type = 'person')
    {
        switch ($type){
            case 'person':
                $this->insertPerson();
                break;
            case 'artist':
                return $this->redirect(['action' => 'insertArtist']);
                break;
            case 'institution':
                return $this->redirect(['action' => 'insertInstitution']);
                break;
            case 'category':
                return $this->redirect(['action' => 'insertCategory']);
        }
    }

    public function insertPerson()
    {
        $cardfile = $this->PersonCardsTable()->newEntity();
        if($this->request->is('post')){
            $cardfile = $this->PersonCardsTable()->patchEntity($cardfile, $this->request->getData());
            osd($cardfile);die;
            If($this->PersonCardsTable()->save($cardfile)){
                $this->Flash->success(__("The $type has been saved."));
                return $this->redirect(['action' => 'index']);
            }
        }
        $this->set(compact('cardfile'));
        $this->render('insert_person');
    }

    public function insertArtist()
    {
        $cardfile = $this->PersonCardsTable()->newEntity();
        if($this->request->is('post')){
            $cardfile = $this->PersonCardsTable()->patchEntity($cardfile, $this->request->getData());
            osd($cardfile);die;
            If($this->PersonCardsTable()->save($cardfile)){
                $this->Flash->success(__("The $type has been saved."));
                return $this->redirect(['action' => 'index']);
            }
        }
        $this->set(compact('cardfile'));
        $this->render('insert_artist');
    }

    public function remove($id)
    {

    }

    public function change($id)
    {

    }

    public function add($type = null)
    {
        switch ($type) {
            case 'category':
                return $this->redirect(['action' => 'addCategory']);
                break;
            default:
                break;
        }

        if ($this->request->is('post')) {
            $card = $this->RolodexCard->patchEntity($card, $this->request->getData());
            if ($this->RolodexCard->save($card)) {
                $this->Flash->success(__('The person has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The person could not be saved. Please, try again.'));
        }
        $members = $this->RolodexCard->Identity->find('list', ['limit' => 200]);
//        $memberUsers = $this->RolodexCard->MemberUsers->find('list', ['limit' => 200]);
//        $this->set(compact('card', 'members', 'memberUsers'));
        $this->set(compact('members'));

    }

    /**
     * Add a new Category member and link it to manager delegates
     *
     * This add page handles the special Category type Member record.
     * The page that is rendered will show any available manager delegates
     * for this supervisor and will allow them to be linked to the new
     * Category during the creation process. This link is the way
     * a supervisor shares contact cards with managers
     *
     * @todo add share link data to... what was i thinking when i wrote this?
     *
     * @return \Cake\Http\Response
     */
    public function addCategory()
    {
        $supervisor_id = $this->contextUser()->getId('supervisor');
        $supervisor_member = $this->contextUser()
            ->getCard('supervisor')
            ->rootID();
        $member = new Member(['user_id' => $supervisor_id]);
        $managers = $this->contextUser()
            ->getSupervisorsManagers()
            ->toValueList('manager_member');

        if ($this->request->is(['post', 'put'])) {
            $MembersTable = TableRegistry::getTableLocator()->get('Members');

            //process and 'share with manager' checkboxes
            $possibleShares = collection($this->request->getData('permit'));
            $shared = $possibleShares->reduce(
                function($accum, $checked, $manager_id) use ($managers, $supervisor_id, $supervisor_member) {
                    if ($checked && in_array($manager_id, $managers)) {
                        $accum[] = [
                            'user_id' => $supervisor_id,
                            'supervisor_id' => $supervisor_member,
                            'manager_id' => $manager_id,
                        ];
                    }
                    return $accum;
                }, []);

            //assemble the new entity and associated 'shares'
            $categoryDefaults = [
                'first_name' => $this->request->getData('last_name'),
                'member_type' => MemCon::CATEGORY,
                'active' => 1,
                'user_id' => $supervisor_id,
                'share_definitions' => $shared
            ];
            $post = array_merge($this->request->getData(), $categoryDefaults);
            $category = $MembersTable->patchEntity($member, $post);

            if (!$category->hasErrors() && $MembersTable->save($category, ['associated' => ['ShareDefinitions']])) {
                return $this->redirect(['action' => 'view', $category->id]);
            } else {
                $this->Flash->error('Validation or application rule violations were found. Please try again.');
            }
        }

        //fall through to render on new request or faild save
        $managerDelegates = $this->PersonCardsTable()->find(
            'delegatedManagers',
            ['supervisor_id' => $supervisor_id]
        );
        $this->set(compact('member', 'managerDelegates'));
    }

    //</editor-fold>

    /**
     * Card View Page : All Types
     *
     * Selects a template appropriate to the record type being viewed
     *
     * @param $id
     * @return \Cake\Http\Response|null
     */
    public function view($id)
    {
        // Is this user permitted to see this RolodexCard
        if (!$this->permitted('member', $id)) {
            return $this->redirect(['action' => 'index']);
        }

        // What kind of RolodexCard sub-type should we get?
        // get Member member_type and select retrieval method for that type

        $MembersTable = TableRegistry::getTableLocator()->get('Members');

        /* @var Member $member */
        $member = $MembersTable->find()
            ->where(['id' => $id])
            ->contain('ArtistManifests') //@todo this can go away if we flag member with isArtist field
            ->toArray()[0];

        /* @var StackTable $CardTable */
        $this->chooseTableType($member, $CardTable);

        /* @var RolodexCard $rolodexCard */
        $rolodexCard = $CardTable->stacksFor('identity', [$id])->shift();

        $this->set('personCard', $rolodexCard);
        $this->set('contextUser', $this->contextUser());

        if ($rolodexCard->isSupervisor()) {
            $this->render('view');
        } elseif ($rolodexCard->isManager()) {
            $this->render('view');
        } elseif ($rolodexCard->isArtist()) {
            $this->render('view');
        } elseif ($rolodexCard->isPerson()) {
            $this->render('view');
        } elseif ($rolodexCard->isCategory()) {
            $candidates = layer($this->IdentitiesTable()->find('all')
                ->where(['user_id' => $this->contextUser()->getId('supervisor')])
                ->toArray());
            /* @var Layer $candidates */

            $candidates = $candidates->getLayer()
                ->find()
                ->specifyFilter('member_type', 'Person')
                ->toKeyValueList('id', 'name');
            $this->set(compact('candidates'));
            $this->render('category');
        } elseif ($rolodexCard->isOrganization()) {
            $this->render('organization');
        } else {
            $msg = $rolodexCard->rootElement()->member_type . ' did no map to a view in cardfile/view';
            throw new UnknownMemberTypeException($msg);
        }
    }

    //<editor-fold desc="********** Index View Variations">

    /**
     * Index method shows mixed record types
     */
    public function index() {
        $this->set('PrefsObject', $this->Prefs);

        //Get the seed ids
        /* @var Query $seedIdQuery */

        /* @todo make this a finder call that addresses managers, supervisors and shares */
        $seedIdQuery = $this->IdentitiesTable()->find('list')
            ->where(['user_id' => $this->contextUser()->getId('supervisor')]);

        //sets search form vars and adds current post (if any) to query
        $this->userFilter($seedIdQuery);

        $FatGenericCardsTable = TableRegistry::getTableLocator()->get('FatGenericCards');
        /* @var FatGenericCardsTable $FatGenericCardsTable */

        try {
            $fatGenericCards = $this->paginate(
                $FatGenericCardsTable->pageFor('identity', $seedIdQuery->toArray()),
                [
                    'limit' => $this->Prefs->for(PrefCon::PAGINATION_LIMIT),
                    'order' => [$this->Prefs->for(PrefCon::PAGINATION_SORT_PEOPLE) =>
                        $this->Prefs->for(PrefCon::PAGINATION_DIR)],
                    'scope' => 'identities'
                ]
            );
        } catch (NotFoundException $e) {
            return $this->redirect($this->Paginator->showLastPage());
        }

        $this->viewBuilder()->setLayout('index');
        $this->set('cards', $fatGenericCards);
        $this->set('indexModel', $fatGenericCards->getPaginatedTableName());
    }

    /**
     * Index page filtered to Organization Cards
     */
    public function organizations()
    {

        /* @todo make this a finder call that is aware of shares */
        $OrganizationCards = TableRegistry::getTableLocator()->get('OrganizationCards');
        $seedIdQuery = $OrganizationCards
            ->Identities->find('list')
            ->where(['user_id' => $this->contextUser()->getId('supervisor')]);

        //sets search form vars and adds current post (if any) to query
        $this->userFilter($seedIdQuery);

        $Prefs = $this->Preferences->getPrefs($this->contextUser()->getId('supervisor'));
        $this->set('PrefsObject', $Prefs);
        $this->viewBuilder()->setLayout('index');
        try {
            $organizationCards = $this->paginate($OrganizationCards->pageFor('identity', $seedIdQuery->toArray()),
                [
                    'limit' => $Prefs->for(PrefCon::PAGINATION_LIMIT),
                    'order' => [$Prefs->for(PrefCon::PAGINATION_SORT_PEOPLE) =>
                        $Prefs->for(PrefCon::PAGINATION_DIR)],
                    'scope' => 'identities'
                ]
            );
        } catch (NotFoundException $e) {
            return $this->redirect($this->Paginator->showLastPage());
        }
        $this->set('organizationCards', $organizationCards);
        $this->set('indexModel', $organizationCards->getPaginatedTableName());
    }

    /**
     * Index page filtered to Grouping/Category Cards
     */
    public function groups() {
        /* @var CategoryCardsTable $CategoryCards */

        /* @todo make this a finder call that is aware of shares */
        $CategoryCards = TableRegistry::getTableLocator()->get('CategoryCards');
        $seedIdQuery = $CategoryCards
            ->Identities->find('list')
            ->where(['user_id' => $this->contextUser()->getId('supervisor')]);

        //sets search form vars and adds current post (if any) to query
        $this->userFilter($seedIdQuery);

        $Prefs = $this->Preferences->getPrefs($this->contextUser()->getId('supervisor'));
        $this->set('PrefsObject', $Prefs);
        $this->viewBuilder()->setLayout('index');
        try {
            $categoryCards = $this->paginate($CategoryCards->pageFor('identity', $seedIdQuery->toArray()),
                [
                    'limit' => $Prefs->for(PrefCon::PAGINATION_LIMIT),
                    'order' => [$Prefs->for(PrefCon::PAGINATION_SORT_PEOPLE) =>
                        $Prefs->for(PrefCon::PAGINATION_DIR)]
                ]
            );
        } catch (NotFoundException $e) {
            return $this->redirect($this->Paginator->showLastPage());
        }
        $this->set('categoryCards', $categoryCards);
        $this->set('indexModel', $categoryCards->getPaginatedTableName());
    }

    /**
     * Index page filtered to Person Cards
     */
    public function people()
    {
        //Get the seed ids
        /* @todo make this a finder call that is aware of shares */
        $seedIdQuery = $this->IdentitiesTable()->find('list',
            ['valueField' => 'id'])
            ->where(['user_id' => $this->contextUser()->getId('supervisor')]);

        //sets search form vars and adds current post (if any) to query
        $this->userFilter($seedIdQuery);

        $PersonCardsTable = TableRegistry::getTableLocator()->get('PersonCards');
        /* @var FatGenericCardsTable $PersonCardsTable */

        $Prefs = $this->Preferences->getPrefs($this->contextUser()->getId('supervisor'));
        $this->set('PrefsObject', $Prefs);
        try {
            $cards = $this->paginate($PersonCardsTable->pageFor('identity', $seedIdQuery->toArray()),
                [
                    'limit' => $Prefs->for(PrefCon::PAGINATION_LIMIT),
                    'order' => [$Prefs->for(PrefCon::PAGINATION_SORT_PEOPLE) =>
                        $Prefs->for(PrefCon::PAGINATION_DIR)],
                    'scope' => 'identities'
                ]
            );
        } catch (NotFoundException $e) {
            return $this->redirect($this->Paginator->showLastPage());
        }

        $this->viewBuilder()->setLayout('index');
        $this->set('cards', $cards);
        $this->set('indexModel', $cards->getPaginatedTableName());
        $this->render('index');
    }

    /**
     * SuperUser use only Index Page showing active registered users
     */
    public function supervisors()
    {
        if(!$this->currentUser()->isSuperuser()) {
            $this->redirect('/pages/no_access');
        }
        $Users = TableRegistry::getTableLocator()->get('Users');
        $PersonCards = TableRegistry::getTableLocator()->get('PersonCards');
        /* @var UsersTable $Users */
        /* @var PersonCardsTable $PersonCards */

        //native person cards for registered users (supervisors)
        $userIdentities = $Users->find('list', ['valueField' => 'member_id'])
            ->where(['active' => 1]);

        $seedIdQuery = $this->IdentitiesTable()->find('list',
            ['valueField' => 'id'])
            ->where(['id IN' => $userIdentities->toArray()]);

        //sets search form vars and adds current post (if any) to query
        $this->userFilter($seedIdQuery);
        $Prefs = $this->Preferences->getPrefs($this->contextUser()->getId('supervisor'));

        try {
            $personCards = $this->paginate(
                $PersonCards->pageFor('identity', $seedIdQuery->toArray()),
                [
                    'limit' => $Prefs->for(PrefCon::PAGINATION_LIMIT),
                    'order' => [$Prefs->for(PrefCon::PAGINATION_SORT_PEOPLE) =>
                        $Prefs->for(PrefCon::PAGINATION_DIR)],
                    'scope' => 'identities'
                ]
            );
        } catch (NotFoundException $e) {
            return $this->redirect($this->Paginator->showLastPage());
        }

        $this->set('PrefsObject', $Prefs);

        $this->viewBuilder()->setLayout('index');
        $this->set('personCards', $personCards);
        $this->set('indexModel', $personCards->getPaginatedTableName());
        $this->render('supervisors');
    }

    //</editor-fold>

    /* @todo Partially stubbed into CardFilter */
    //<editor-fold desc="********** Index Search Filter Tools">

    /**
     * Add user search to index pages
     *
     * This method both prepares the values for the form that
     * is displayed and applies current or save filter requests
     * to the evoloving index page query.
     *
     * @param $query
     * @return Query
     */
    public function userFilter($query)
    {
        if ($this->request->is('post') || $this->request->is('put')) {

            $filter = new CardfileFilter();
            $whereThis = $filter->execute($this->request->getData());

            $query->where($whereThis);
            // persist the filter for future and paginated viewing
            $path = $this->request->getParam('controller') . '.' . $this->request->getParam('action');
            $this->getRequest()->getSession()->write('filter', [
                'path' => $path,
                'conditions' => $whereThis]);
        } elseif (!is_null($this->getRequest()->getSession()->read('filter'))) {
            // respond to stored filters incases there was no post
            $params = $this->getRequest()->getQueryParams();
            $whereThis = $this->getRequest()->getSession()
                ->read("filter.conditions")
                ?? []
            ;
            $query->where($whereThis);
        }
        // set the values needed to render a search/filter for on the index page
        $identities = TableRegistry::getTableLocator()->get('Identities');
        $modes = ['is', 'starts', 'ends', 'contains', 'isn\'t'];
        $identity = $identities->newEntity([]/*, ['validate' => false]*/);
        $identity->modes = $modes;
        $this->set('identitySchema', $identity);
        return $query;
    }

    //</editor-fold>

    /**
     * HACK STUB METHOD
     * @param $type
     * @param $id
     * @return bool
     */
    public function permitted($type, $id)
    {
        $MembersTable = TableRegistry::getTableLocator()->get('Members');
        if (!$MembersTable->exists(['id' => $id])) {
            $this->Flash->error('The requested record does not exist.');
            return FALSE;
        } elseif ('permission check' == FALSE) {
            $this->Flash->error('You don\'t have access to this record.');
            return FALSE;
        }
        return true;
    }

    //<editor-fold desc="********** Table Access Methods">

    /**
     * Lazy load Indentites Table
     *
     * @return IdentitiesTable
     */
    protected function IdentitiesTable()
    {
        if($this->Identities === false){
            $this->Identities = TableRegistry::getTableLocator()->get('Identities');
        }
        return $this->Identities;
    }

    /**
     * Lazy load PersonCards Table
     *
     * @return PersonCardsTable
     */
    protected function PersonCardsTable()
    {
        if($this->PersonCards === false){
            $this->PersonCards = TableRegistry::getTableLocator()->get('PersonCards');
        }
        return $this->PersonCards;
    }

    //</editor-fold>

    /**
     * @param Member $member
     * @param $CardTable
     */
    private function chooseTableType(Member $member, &$CardTable): void
    {
        switch ($member->type()) {
            case MEMBER_TYPE_CATEGORY:
                $CardTable = TableRegistry::getTableLocator()->get('CategoryCards');
                break;

            case MEMBER_TYPE_ORGANIZATION:
                $CardTable = TableRegistry::getTableLocator()->get('OrganizationCards');
                break;

            case MEMBER_TYPE_PERSON:
                // A person might be an artist. That has a special Stack which includes artworks
                if (count($member->artist_manifests) > 0) {
                    $CardTable = TableRegistry::getTableLocator()->get('ArtistCards');
                } else {
                    $CardTable = $this->PersonCardsTable();
                }
                break;

            default:
                $msg = "The requested record was of unknown type: {$member->type()}";
                throw new UnknownMemberTypeException($msg);
                break;
        }
    }

}

<?php
namespace App\Controller;

use App\Exception\BadMemberRecordType;
use App\Model\Entity\Member;
use App\Model\Entity\RolodexCard;
use App\Model\Entity\Manifest;
use App\Model\Lib\Layer;
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

/**
 * CakePHP CardFileController
 * @author dondrake
 * @property PersonCard $PersonCard
 * @property RolodexCard $RolodexCard
 * @property RolodexCardsTable $RolodexCards
 */
class CardFileController extends AppController {

    public $name = 'CardFile';
    /**
     * @var bool|IdentitiesTable
     */
    protected $Identities = false;

    /**
     * @var bool|PersonCardsTable
     */
    protected $PersonCards = false;

    public function initialize() {
        parent::initialize();
    }

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

    /**
     * Index method
     *
     *
     */
    public function index() {
        //Get the seed ids
        /* @var Query $seedIdQuery */

        $seedIdQuery = $this->IdentitiesTable()->find('list')
            ->where(['user_id' => $this->contextUser()->getId('supervisor')]);

        //sets search form vars and adds current post (if any) to query
        $this->cardSearch($seedIdQuery);

        $FatGenericCardsTable = TableRegistry::getTableLocator()->get('FatGenericCards');
        /* @var FatGenericCardsTable $FatGenericCardsTable */

        $fatGenericCards = $this->paginate($FatGenericCardsTable->pageFor('identity', $seedIdQuery->toArray()));

        $this->set('cards', $fatGenericCards);
    }

    public function organizations()
    {
        $OrganizationCards = TableRegistry::getTableLocator()->get('OrganizationCards');
        $seedIdQuery = $OrganizationCards
            ->Identities->find('list')
            ->where(['user_id' => $this->contextUser()->getId('supervisor')]);

        //sets search form vars and adds current post (if any) to query
        $this->cardSearch($seedIdQuery);

        $organizationCards = $this->paginate($OrganizationCards->pageFor('identity', $seedIdQuery->toArray));
        $this->set('organizationCards', $organizationCards);
    }

    public function people()
    {
        //Get the seed ids
        $seedIdQuery = $this->IdentitiesTable()->find('list',
            ['valueField' => 'id'])
            ->where(['user_id' => $this->contextUser()->getId('supervisor')]);

        //sets search form vars and adds current post (if any) to query
        $this->cardSearch($seedIdQuery);

        $PersonCardsTable = TableRegistry::getTableLocator()->get('PersonCards');
        /* @var FatGenericCardsTable $PersonCardsTable */

        $cards = $this->paginate($PersonCardsTable->pageFor('identity', $seedIdQuery->toArray()));

        $this->set('cards', $cards);
        $this->render('index');
    }

    public function insert($type = 'person')
    {
        $type = 'person';
        $type = 'artist';
        $type = 'institution';
        $type = 'category';
    }

    public function remove($id)
    {

    }

    public function change($id)
    {

    }

    /**
     * a Search-aware page of group/category cards
     */
    public function groups() {
        /* @var CategoryCardsTable $CategoryCards */

        $CategoryCards = TableRegistry::getTableLocator()->get('CategoryCards');
        $seedIdQuery = $CategoryCards
            ->Identities->find('list')
            ->where(['user_id' => $this->contextUser()->getId('supervisor')]);

        //sets search form vars and adds current post (if any) to query
        $this->cardSearch($seedIdQuery);

        $categoryCards = $this->paginate($CategoryCards->pageFor('identity', $seedIdQuery->toArray()));
        $this->set('categoryCards', $categoryCards);
    }

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
    public function view($id)
    {
        /* @var StackSet $personCards */
        /* @var PersonCard $personCard */
        /* @var ManifestsTable $ManifestsTable */
        /* @var StackTable $CardTable */

        // Is this user permitted to see this RolodexCard
        if (!$this->permitted('member', $id)) {
            return $this->redirect(['action' => 'index']);
        }

        // What kind of RolodexCard sub-type should we get?
        // get Member member_type and select retrieval method for that type

        $MembersTable = TableRegistry::getTableLocator()->get('Members');
        $member = $MembersTable->find()
            ->where(['id' => $id])
            ->contain('ArtistManifests') //@todo this can go away if we flag member with isArtist field
            ->toArray()[0];

        /* @var Member $member */

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
                throw new BadMemberRecordType($msg);
                break;
        }

        $rolodexCard = $CardTable->stacksFor('identity', [$id])->shift();
        /* @var RolodexCard $rolodexCard */

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
            $this->render('category');
        } elseif ($rolodexCard->isOrganization()) {
            $this->render('organization');
        } else {
            $msg = $rolodexCard->rootElement()->member_type . ' did no map to a view in cardfile/view';
            throw new BadMemberRecordType($msg);
        }
    }

    /**
     * Index page of supervisors for SuperUser use only
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
        $seedIdQuery = $Users->find('list', ['valueField' => 'member_id']);

        //sets search form vars and adds current post (if any) to query
        $this->cardSearch($seedIdQuery);

        $personCards = $this->paginate(
            $PersonCards->pageFor('identity', $seedIdQuery->toArray())
        );

        $this->set('personCards', $personCards);
        $this->render('supervisors');
    }

    public function add()
    {
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
     * Add user search to member-record finds
     *
     * @param $query
     * @return Query
     */
    public function cardSearch($query)
    {
        if ($this->request->is('post') || $this->request->is('put')) {
            $post = $this->request->getData();
            $conditions = [];
            foreach (['first', 'last'] as $key) {
                $input = $post["{$key}_name"];
                if (!empty($input)) {
                    $conditions += $this->condition($key, $input, $post);
                }
            }
            if (!empty($conditions)){
                $query->where(['OR' => $conditions]);
            }
        }
        $identities = TableRegistry::getTableLocator()->get('Identities');
        $modes = ['is', 'starts', 'ends', 'contains', 'isn\'t'];
        $identity = $identities->newEntity([]);
        $identity->modes = $modes;
        $this->set('identitySchema', $identity);
        return $query;
    }

    /**
     * Construct a single condition from user search
     * @param $key
     * @param $input
     * @param $data
     * @return array
     */
    private function condition($key, $input, $data)
    {
        switch ($data["{$key}_name_mode"]) {
            case 0: //is
                $condition = ["{$key}_name" => $input];
                break;
            case 1: //starts
                $condition = ["{$key}_name LIKE" => "$input%"];
                break;
            case 2: //ends
                $condition = ["{$key}_name LIKE" => "%$input"];
                break;
            case 3: //contains
                $condition = ["{$key}_name LIKE" => "%$input%"];
                break;
            case 4: //isn't
                $condition = ["{$key}_name !=" => "$input"];
                break;
            default:
                $condition = [];
                break;
        }
        return $condition;
    }
}

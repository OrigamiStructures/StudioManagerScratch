<?php
namespace App\Controller;

use App\Exception\BadMemberRecordType;
use App\Model\Entity\Member;
use App\Model\Entity\RolodexCard;
use App\Model\Entity\Manifest;
use App\Model\Lib\Layer;
use App\Model\Table\ManifestsTable;
use Cake\ORM\TableRegistry;
use App\Model\Entity\PersonCard;
use App\Model\Lib\StackSet;

/**
 * CakePHP RolodexCardsController
 * @author dondrake
 * @property PersonCard $PersonCard
 * @property RolodexCard $RolodexCard
 */
class RolodexCardsController extends AppController {

	public $name = 'RolodexCards';

	public function initialize() {
		parent::initialize();
		$this->PersonCards = TableRegistry::getTableLocator()->get('PersonCards');
		$this->RolodexCard = TableRegistry::getTableLocator()->get('RolodexCards');
	}

	public function index() {
		$ids = $this->RolodexCards->Identities->find('list')->toArray();
//		$personCards = $this->PersonCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
		$personCards = $this->PersonCards->pageFor('identity', $ids);
		$this->set('personCards', $personCards);
	}

	public function groups() {
		$InstitutionCards = TableRegistry::getTableLocator()->get('OrganizationCards');
		$ids = $InstitutionCards
				->Identities->find('list')
//				->where(['member_type' => 'Institution'])
				->toArray();
		$institutionCards = $InstitutionCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
		$this->set('institutionCards', $institutionCards);
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
            ->contain('ArtistManifests')
            ->toArray()[0];

        /* @var Member $member */

        switch ($member->type()) {
            case 'Category':
                $CardTable = TableRegistry::getTableLocator()->get('CategoryCard');
                break;

            case 'Institution':
                $CardTable = TableRegistry::getTableLocator()->get('InstitutionCard');
                break;

            case 'Person':
                // A person might be an artist. That has a special Stack which includes artworks
                if (count($member->artist_manifests) > 0) {
                    $CardTable = TableRegistry::getTableLocator()->get('ArtistCards');
                } else {
                    $CardTable = $this->PersonCards;
                }
                break;

            default:
                $msg = "The requested record was of unknown type: {$member->type()}";
                throw new BadMemberRecordType($msg);
                break;
        }

        $rolodexCard = $CardTable->find('stacksFor',  ['seed' => 'identity', 'ids' => [$id]]);
        $rolodexCard = $rolodexCard->shift();

        $this->set('personCard', $rolodexCard);
        $this->set('contextUser', $this->contextUser());
	}

    public function supervisors()
    {
        if(!$this->currentUser()->isSuperuser()) {
            $this->redirect('/pages/no_access');
        }
        $this->index();
        $this->render('index');
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
        $members = $this->RolodexCard->find('list', ['limit' => 200]);
//        $memberUsers = $this->RolodexCard->MemberUsers->find('list', ['limit' => 200]);
//        $this->set(compact('card', 'members', 'memberUsers'));
        $this->set(compact('card', 'members'));

    }
}

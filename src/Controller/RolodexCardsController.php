<?php
namespace App\Controller;

use App\Model\Entity\Manifest;
use App\Model\Entity\Member;
use App\Model\Table\IdentitiesTable;
use App\Model\Table\ManifestsTable;
use App\Model\Lib\Layer;
use App\Model\Entity\RolodexCard;
use App\Model\Table\RolodexCardsTable;
use Cake\Collection\Collection;
use Cake\ORM\TableRegistry;
use App\Model\Lib\StackSet;
use App\Model\Entity\PersonCard;
use App\Model\Table\PersonCardsTable;

/**
 * CakePHP RolodexCardsController
 * @author dondrake
 * @property PersonCard $PersonCard
 * @property RolodexCard $RolodexCard
 * @property RolodexCardsTable $RolodexCards
 * @property PersonCardsTable $PersonCards
 * @property IdentitiesTable $Identities
 */
class RolodexCardsController extends AppController {

	public $name = 'RolodexCards';

	public function initialize() {
		parent::initialize();
		$this->PersonCards = TableRegistry::getTableLocator()->get('PersonCards');
		$this->RolodexCards = TableRegistry::getTableLocator()->get('RolodexCards');
	}

	public function index() {
		$ids = $this->RolodexCards->Identities->find('list')->toArray();
		$personCards = $this->PersonCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
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

    public function view($id)
    {
        /* @var StackSet $personCards */
        /* @var PersonCard $personCard */
        /* @var ManifestsTable $ManifestsTable */
        /* @var StackTable $CardTable */


        // Is this user permitted to see this RolodexCard
        if (!$this->permited('member', $id)) {
            $this->Flash->error('You don\'t have access to this record.');
            $this->redirect($this->referer());
        }

        // What kind of RolodexCard sub-type should we get?
        // get Member member_type and select retrieval method for that type

        $MembersTable = TableRegistry::getTableLocator()->get('Members');
        $member = $MembersTable->get($id);

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
                $ManifestsTable = TableRegistry::getTableLocator()->get('Manifests');
                $manifest = $ManifestsTable->find('first')
                    ->where(['member_id' => $id]);

                if (count($manifest) == 1) {
                    $CardTable = TableRegistry::getTableLocator()->get('Manifests');
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

        $this->set('rolodexCard', $rolodexCard);
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
        /**
         * @var StackSet $potentialArtistsStackSet
         */
        if ($this->request->is('post')) {
            $card = $this->RolodexCard->patchEntity($card, $this->request->getData());
            if ($this->RolodexCard->save($card)) {
                $this->Flash->success(__('The person has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The person could not be saved. Please, try again.'));
        }

        $potentialArtistsQuery = $this->RolodexCards->Identities->find('list',
            ['valueField' => 'id'])
            ->where([
                'Identities.user_id' => $this->contextUser()->getId('supervisor'),
                'Identities.member_type' => 'Person'
            ]);

        $potentialArtistsStackSet = $this->PersonCards
            ->find('stacksFor',[
                'seed' => 'identity',
                'ids' => $potentialArtistsQuery->toArray()
            ]);

        $peopleCollection = new Collection($potentialArtistsStackSet->getData());

        $nonArtists = $peopleCollection->reduce(function($accum, $PersonCard){
            if (!$PersonCard->isArtist()){
                $accum[$PersonCard->rootId()] = $PersonCard->rootElement()->name();
            }
            return $accum;
        },[]);

        $this->set(compact('card', 'nonArtists'));
    }
}

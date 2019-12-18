<?php
namespace App\Controller;

use App\Model\Entity\RolodexCard;
use App\Model\Entity\Manifest;
use App\Model\Lib\Layer;
use App\Model\Table\RolodexCardsTable;
use Cake\ORM\TableRegistry;
use App\Model\Entity\PersonCard;
use App\Model\Lib\StackSet;

/**
 * CakePHP RolodexCardsController
 * @author dondrake
 * @property PersonCard $PersonCard
 * @property RolodexCard $RolodexCard
 * @property RolodexCardsTable $RolodexCards
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

        $personCards = $this->PersonCards->find('stacksFor',  ['seed' => 'identity', 'ids' => [$id]]);
        $personCard = $personCards->shift();

        if ($personCard->isArtist()) {
            $ArtworksTable = TableRegistry::getTableLocator()->get('Artworks');
            $artworks = $ArtworksTable->find('all')
                ->where(['member_id' => $id])
                ->toArray();
            $personCard->artworks = new Layer($artworks, 'artwork');
        }

        /*
         * Get an id => name list to support all members mentioned in manifests
         */
        if($personCard->hasManifests()) {
            $ManifestTable = TableRegistry::getTableLocator()->get('Manifests');
            $names = $ManifestTable->find(
                'NameOfParticipants',
                ['manifests' => $personCard->getManifests()->toArray()
                ]);
            $this->set('names', $names);
        }

        if ($personCard->isManager()) {
            $actingUserId = $this->contextUser()->getId('supervisor');
            $receivedManagement = $personCard->receivedManagement($actingUserId);
            $delegatedManagement = $personCard->delegatedManagement($actingUserId);
            $this->set(compact('receivedManagement', 'delegatedManagement'));
        }

        if ($personCard->isSupervisor()) {

        }

        $this->set('personCard', $personCard);
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
        //Find a list of members/people/rolodexes that belong to the user but aren't currently assigned
        //as a member_id to any manifest
        //that would be a list of non-artist members/people/rolodexes

        $potentialArtists = $this->RolodexCards->Identities->find('all')
            ->contain(['Manifests'])
            ->where([
                'Identities.user_id' => $this->contextUser()->getId('supervisor'),
                'Identities.member_type' => 'Person',
                'Manifests.id' => NULL
            ]);

        osd(sql($potentialArtists));
        $layerOfPotentialArtists = \layer($potentialArtists->toArray());
        osd($layerOfPotentialArtists->toKeyValueList('id', 'name'));die;
        $members = $this->RolodexCard->find('list', ['limit' => 200]);
        osd(sql($members));
        osd($members->toArray());die;
//        $memberUsers = $this->RolodexCard->MemberUsers->find('list', ['limit' => 200]);
//        $this->set(compact('card', 'members', 'memberUsers'));
        $this->set(compact('card', 'members'));

    }
}

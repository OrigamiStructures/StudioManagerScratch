<?php
namespace App\Controller;

use App\Model\Entity\Manifest;
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

        $personCards = $this->PersonCards->find('stacksFor',  ['seed' => 'identity', 'ids' => [$id]]);
        $personCard = $personCards->shift();

        if ($personCard->isArtist()) {
            $ArtworksTable = TableRegistry::getTableLocator()->get('Artworks');
            $artworks = $ArtworksTable->find('all')
                ->where(['member_id' => $id])
                ->toArray();
            $personCard->artworks = new Layer($artworks, 'artwork');
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

<?php
namespace App\Controller;

use App\Model\Entity\RolodexCard;
use App\Model\Lib\Layer;
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
        /*  @var StackSet $personCards */
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

        $this->set('personCard', $personCard);
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

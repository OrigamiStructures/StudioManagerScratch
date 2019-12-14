<?php
namespace App\Controller;

use App\Model\Entity\Manifest;
use App\Model\Lib\Layer;
use Cake\ORM\TableRegistry;
use App\Model\Entity\PersonCard;
use App\Model\Lib\StackSet;

/**
 * CakePHP RolodexCardsController
 * @author dondrake
 */
class RolodexCardsController extends AppController {

	public $name = 'RolodexCards';

	public function initialize() {
		parent::initialize();
		$this->PersonCards = TableRegistry::getTableLocator()->get('PersonCards');
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
            $recievedManagement = $personCard->recievedManagement($actingUserId);
            $delegatedManagement = $personCard->delegatedManagement($actingUserId);
            $this->set(compact('recievedManagement', 'delegatedManagement'));
        }

        if ($personCard->isSupervisor()) {

        }

        $this->set('personCard', $personCard);
        $this->set('contextUser', $this->contextUser());
	}
}

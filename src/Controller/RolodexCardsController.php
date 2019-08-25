<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;

/**
 * CakePHP RolodexCardsController
 * @author dondrake
 */
class RolodexCardsController extends AppController {
	
	public function initialize() {
		parent::initialize();
		$this->RolodexCards = TableRegistry::getTableLocator()->get('RolodexCards');
	}
	
	public function index() {
		$ArtistManifests = TableRegistry::getTableLocator()->get('ArtistManifests');
		$stacks = $ArtistManifests->find('stacksFor', ['seed' => 'identity', 'ids' => [1]]);
		
		$ids = $this->RolodexCards->Identities->find('list')->toArray();
		$rolodexCards = $this->RolodexCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
		$this->set('rolodexCards', $rolodexCards);
	}
	
	public function groups() {
		$InstitutionCards = TableRegistry::getTableLocator()->get('OrganizationCards');
		$ids = $InstitutionCards
				->Identities->find('list')
//				->where(['member_type' => 'Institution'])
				->toArray();
		$instutionCards = $InstitutionCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
		$this->set('instutionCards', $instutionCards);
	}
}

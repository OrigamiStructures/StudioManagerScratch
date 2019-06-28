<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;

/**
 * CakePHP RolodexCardsController
 * @author dondrake
 */
class RolodexCardsController extends AppController {
	
	public function index() {
		$ArtistManifests = TableRegistry::getTableLocator()->get('ArtistManifests');
		$stacks = $ArtistManifests->find('stacksFor', ['seed' => 'identity', 'ids' => [1]]);
		osd($stacks);

		
		$ids = $this->RolodexCards->Identities->find('list')->toArray();
		$rolodexCards = $this->RolodexCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
		$this->set('rolodexCards', $rolodexCards);
	}
	
	public function groups() {
		$CategoryCards = TableRegistry::getTableLocator()->get('OrganizationCards');
		$ids = $CategoryCards
				->Identities->find('list')
				->where(['member_type' => 'Institution'])
				->toArray();
		$categoryCards = $CategoryCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
//		osd($categoryCards);die;
		$this->set('categoryCards', $categoryCards);
	}
}

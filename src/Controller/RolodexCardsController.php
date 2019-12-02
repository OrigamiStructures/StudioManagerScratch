<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;

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
		$rolodexCards = $this->PersonCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
		$this->set('rolodexCards', $rolodexCards);
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
}

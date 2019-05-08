<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

/**
 * CakePHP RolodexCardsController
 * @author dondrake
 */
class RolodexCardsController extends AppController {
	
	public function index() {
		$ArtistManifests = $this->getTableLocator()->get('ArtistManifests');
		$stacks = $ArtistManifests->find('stacksFor', ['seed' => 'identity', 'ids' => [1]]);
		osd($stacks);

		
		$ids = $this->RolodexCards->Identities->find('list')->toArray();
		$rolodexCards = $this->RolodexCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
		$this->set('rolodexCards', $rolodexCards);
	}
	
	public function groups() {
		$CategoryCards = $this->getTableLocator()->get('OrganizationCards');
		$ids = $CategoryCards
				->Identities->find('list')
				->where(['member_type' => 'Institution'])
				->toArray();
		$categoryCards = $CategoryCards->find('stacksFor',  ['seed' => 'identity', 'ids' => $ids]);
//		osd($categoryCards);die;
		$this->set('categoryCards', $categoryCards);
	}
}

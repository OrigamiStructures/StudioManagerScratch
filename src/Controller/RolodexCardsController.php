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
//		$this->set('rolodexCards', $this->RolodexCards->Memberships->find('all')->contain(['Members']));
//		$this->set('rolodexCards', $this->RolodexCards->find('all')->contain(['DataOwner', 'Memberships']));
		$ids = $this->RolodexCards->Identities->find('list')->toArray();
//		osd($ids);
		$rolodexCards = $this->RolodexCards->find('stackFrom',  ['layer' => 'identity', 'ids' => $ids]);
		$this->set('rolodexCards', $rolodexCards);
//		$this->set('rolodexCards', $this->RolodexCards->Identities->find('all'));
	}
}

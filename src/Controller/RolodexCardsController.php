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
		$this->RolodexCards->find('stackFrom',  ['layer' => 'identity', 'ids' => [1,2,3,4,5]]);
		$this->set('rolodexCards', $rolodexCards);
	}
}

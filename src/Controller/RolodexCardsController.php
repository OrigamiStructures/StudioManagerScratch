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
	public function testMe() {
		
		$ids = [1,2];
		$cards = $this->RolodexCards->find('stackFrom', ['layer' => 'member', 'ids' => $ids]);
		$this->set('cards', $cards);
//		$this->labeled(
//				$cards->load('member',['member_type', 'Institution'])
//				);
//
//		$this->labeled(
//				$cards->load('member',['member_type', 'Person'])
//				);
//
//		die;
	}
	
	public function labeled($cards) {
		foreach($cards as $card) {
			echo "<p>{$card->getName(LABELED)}</p>";
		}
	}
}

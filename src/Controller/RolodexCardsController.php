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
		
		$ids = range(1, 73);
		$cards = $this->RolodexCards->find('stackFrom', ['layer' => 'member', 'ids' => $ids]);

		foreach($cards->all() as $card) {
			echo "<p>{$card->getName(LABELED)}</p>";
		}
		
		die;
		
	}
}

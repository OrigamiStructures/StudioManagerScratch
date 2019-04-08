<?php


namespace App\Controller;

use App\Model\Table\MembersTable as RolodexCardsDev;

/**
 * CakePHP RolodexCardsDevController
 * @author dondrake
 */
class RolodexCardsDevController extends AppController {
	
	public function initialize() {
		parent::initialize();
//		$this->loadModel('RolodexCardsDev');
	}
	public function testMe() {
		
		$ids = [1,2,21,20,19,22];
		$cards = $this->RolodexCardsDev->find('stackFrom', ['layer' => 'member', 'ids' => $ids]);
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

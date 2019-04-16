<?php
namespace App\Model\Table;

use App\Model\Table\RolodexCardsTable;

use Cake\ORM\Table;
use App\Model\Traits\ContactableTableTrait;
use App\Model\Traits\ReceiverTableTrait;

/**
 * CakePHP PersonCardsTable
 * @author dondrake
 */
class PersonCardsTable extends RolodexCardsTable {
	
	
	use ContactableTableTrait;
	use ReceiverTableTrait;
	
	public function initialize(array $config) {
		$this->initializeContactableCard();
		$this->initializeReceiverCard();
		$this->addLayerTable(['Images']);
		$this->stackSchema[] = ['name' => 'image',	'specs' => ['type' => 'layer']];
		$this->seedPoints = array_merge($this->seedPoints, ['image', 'images']);
		parent::initialize($config);
	}

//	public function initialize(array $config) {
//		parent::initialize($config);
//		$this->$stackSchema += [
//			['name' => 'artist',			'specs' =>['type' => 'layer']],
//			['name' => 'managers',			'specs' =>['type' => 'layer']],
//			['name' => 'managed_artists',	'specs' =>['type' => 'layer']],
//		];
//		$this->seedPoints = array_merge($this->seedPoints, [
//			'artist', 
//			'managers', 
//			'managed_artists']);
//	}
	
}

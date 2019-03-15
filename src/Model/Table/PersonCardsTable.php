<?php
namespace App\Model\Table;

use App\Model\Table\RolodexCardsTable;

use Cake\ORM\Table;

/**
 * CakePHP PersonCardsTable
 * @author dondrake
 */
class PersonCardsTable extends RolodexCardsTable {
	
	public function initialize(array $config) {
		parent::initialize($config);
		$this->$stackSchema += [
			['name' => 'artist',			'specs' =>['type' => 'layer']],
			['name' => 'managers',			'specs' =>['type' => 'layer']],
			['name' => 'managed_artists',	'specs' =>['type' => 'layer']],
		];
		$this->seedPoints = array_merge($this->seedPoints, [
			'artist', 
			'managers', 
			'managed_artists']);
	}
	
	public function loadFromMember($id) {
		parent::loadFromMember($ids);
		// call for additional construction
	}
	
}

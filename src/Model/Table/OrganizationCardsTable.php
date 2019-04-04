<?php

namespace App\Model\Table;

use App\Model\Table\CategoryCardsTable;
use App\Model\Traits\ContactableTrait;
use App\Model\Traits\ReceiverTrait;

/**
 * CakePHP OrganizationCardsTable
 * @author dondrake
 */
class OrganizationCardsTable extends CategoryCardsTable {
	
	use ContactableTrait;
	use ReceiverTrait;
	
	public function initialize(array $config) {
		$this->initializeContactableCard();
		$this->initializeReceiverCard();
//		$this->layerTables[] = 'Members';
//		$this->stackSchema[] = ['name' => 'members',	'specs' => ['type' => 'layer']];
//		$this->seedPoints = array_merge($this->seedPoints, ['member', 'members']);
		parent::initialize($config);
	}
}

<?php

namespace App\Model\Table;

use App\Model\Table\CategoryCardsTable;
use App\Model\Traits\ContactableTableTrait;
use App\Model\Traits\ReceiverTableTrait;

/**
 * CakePHP OrganizationCardsTable
 * @author dondrake
 */
class OrganizationCardsTable extends CategoryCardsTable {
	
	use ContactableTableTrait;
	use ReceiverTableTrait;
	
	public function initialize(array $config) {
		parent::initialize($config);
		$this->initializeContactableCard();
		$this->initializeReceiverCard();
	}
}

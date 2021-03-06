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

	protected function localConditions($query, $options = []) {
		return $query->where(['member_type' => MEMBER_TYPE_ORGANIZATION]);
	}

	protected function marshalIdentity($id, $stack) {
			$identity = $this->Identities
                ->find('all')
                ->where(['id' => $id, 'member_type' => MEMBER_TYPE_ORGANIZATION]);
			$stack->set(['identity' => $identity->toArray()]);
			return $stack;
	}

}

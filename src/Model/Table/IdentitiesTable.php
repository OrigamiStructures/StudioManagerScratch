<?php

namespace App\Model\Table;

use App\Model\Table\AppTable;

/**
 * CakePHP IdentitiesTable
 * @author dondrake
 */
class IdentitiesTable extends AppTable {
	
	public function initialize(array $config) {
		$this->setTable('members');
		parent::initialize($config);
	}
	
}

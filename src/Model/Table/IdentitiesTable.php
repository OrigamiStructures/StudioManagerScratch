<?php

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * CakePHP IdentitiesTable
 * @author dondrake
 */
class IdentitiesTable extends Table {
	
	public function initialize(array $config) {
		$this->setTable('members');
		parent::initialize($config);
	}
	
}

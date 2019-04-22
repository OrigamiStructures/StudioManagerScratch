<?php

namespace App\Model\Table;

use App\Model\Table\AppTable;

/**
 * CakePHP IdentitiesTable
 * @author dondrake
 */
class GroupIdentitiesTable extends IdentitiesTable {
	
	public function initialize(array $config) {
		$this->setTable('members');
		parent::initialize($config);
	}
	// NO FUNCTIONAL. 
	// ATTEMPTED TO MAKE THIS THE TABLE TYPE FOR ROLODEX::MEMBERSHIPS
}

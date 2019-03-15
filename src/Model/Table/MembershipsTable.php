<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;

/**
 * CakePHP Memberships
 * @author dondrake
 */
class MembershipsTable extends Table {
	
	public function initialize(array $config) {
		$this->setTable('groups');
//		$this->initializeAssociations();
		parent::initialize($config);
	}
	
//	public function initializeAssociations() {
//		$this->belongsTo('Groups')
//				->setForeignKey('group_id');
//	}
	
	public function findHook(Query $query, array $options) {
		return $query;
//		->select(['group_id', 'member_id'])
//			->contain(['Groups']);
	}
	
}

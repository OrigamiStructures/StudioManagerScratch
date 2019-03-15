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
		$this->initializeAssociations();
		parent::initialize($config);
	}
	
	public function initializeAssociations() {
		$this->belongsTo('ProxyMembers')
			->setForeignKey('member_id');
	}
	
	public function findHook(Query $query, array $options) {
		return $query
				->contain(['ProxyMembers'])
				->select([
					'Memberships.id', 
					'Memberships.member_id', 
					'GroupsMembers.member_id', 
					'GroupsMembers.group_id',
					'ProxyMembers.id',
					'ProxyMembers.first_name',
					'ProxyMembers.last_name'])
				;
	}
	
}

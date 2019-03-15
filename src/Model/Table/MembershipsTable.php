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
		$this->belongsTo('GroupIdentities')
			->setForeignKey('member_id');
	}
	
	public function findHook(Query $query, array $options) {
		return $query
				->contain(['GroupIdentities'])
				->select([
					'Memberships.id', 
					'Memberships.member_id', 
					'GroupsMembers.member_id', 
					'GroupsMembers.group_id',
					'GroupIdentities.id',
					'GroupIdentities.first_name',
					'GroupIdentities.last_name'])
				;
	}
	
}

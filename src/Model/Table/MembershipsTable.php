<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;

/**
 * Memberships Table
 * 
 * This table reveals any Memberships a Rolodex Card has.
 * 
 * This table uses the Groups repository as Memberships. Since that table 
 * has only basic info about the groupiness of a Member record (another 
 * RolodexCard), this table establishes an association to the Members 
 * repository so the identity of this group can be known. So, in this 
 * table context Members is known as GroupIdentity.
 * 
 * CakePHP Memberships
 * @author dondrake
 */
class MembershipsTable extends Table {

	public function initialize(array $config) {
		$this->setTable('members');
//		$this->initializeAssociations();
		parent::initialize($config);
	}

	protected function _initializeAssociations() {
        $this->belongsToMany(
			'RolodexCards', 
			['joinTable' => 'groups_members'])
			->setForeignKey('group_id')
			->setTargetForeignKey('member_id')
//			->setProperty('members')
			;
	}

	/**
	 * Get necessary link and identity data fro a Group
	 * 
	 * When creating a RolodexCard object to fully describe a Person 
	 * Organization, or Category, one fact we need is that Cards memberships 
	 * in Organizations or Categories. This hook will bring those owning-groups 
	 * to light in an entity.
	 * 
	 * The Group record is mostly discarded, its few needed data points 
	 * being placed in the GroupIdentity (Member) entity (see the mapper 
	 * function). The GroupIdentity entity will add access to this data 
	 * in its extension of Member.
	 * 
	 * @param Query $query
	 * @param array $options
	 * @return array
	 */
	public function findHook(Query $query, array $options) {
		
		$result = $query
            ->where(['active' => 1])
//			->contain(['GroupIdentities'])
//			->mapReduce($this->hookMapper(), $this->dummyReducer())
//            ->toArray()
			;
//		return $result['groupIdentities'];
		return $result;
	}
	
//	protected function hookMapper() {
//		return function ($group, $key, $mapReduce) {
//			$groupIdentity = $group->group_identity;
//			$groupIdentity->group_id = $group->id;
//			$groupIdentity->group_active = $group->active;
//			unset($groupIdentity->created, $groupIdentity->modified);
//			$group = $groupIdentity;
//			$group->clean();
//			
//			$mapReduce->emitIntermediate($group, 'groupIdentities');
//		};
//	}
//	
//	protected function dummyReducer() {
//		return function ($group, $groupIdentity, $mapReduce) {
//			$mapReduce->emit($group, 'groupIdentities');
//		};
//	}

}

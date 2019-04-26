<?php

namespace App\Model\Table;

use App\Model\Table\ArtistCardsTable;

/**
 * CakePHP OrganizationCardsTable
 * @author dondrake
 */
class UserCardsTable extends ArtistCardsTable {
		
	public function initialize(array $config) {
		parent::initialize($config);
		$this->addStackSchema([
			'managed_artists', 
			'permissions',
			'rolodex']);
		$this->addSeedPoint(
			[
				'user', 
				'users', 
				'managed_artist', 
				'managed_artists', 
				'permission', 
				'permissions',
				'rolodex'
			]
		);
	}
	
	/**
	 * The rolodex cards for a user ? :
	 *		Any contacts/members
	 *		Any managed artists
	 *		Any managers
	 * 
	 * @param type $ids
	 */
	
	protected function distillFromManagedAritst($ids) {
		$IDs = $this->Artists->find('list', ['valueField' => 'member_id'])
				->where(['manager_id IN' => $ids])
				->toArray();
		return array_unique($IDs);
	}
	
	protected function distillFromPermission($ids) {
		
		$IDs = $this->Permissions->find('list', ['valueField' => 'member_id'])
				->where(['id IN' => $ids])
				->toArray();
		return array_unique($IDs);
	}
	
	protected function distillFromRolodex($ids) {
		
	}
	
	protected function marshalManagedArtists($id, $stack) {
		
	}
	
	protected function marshalPermissions($id, $stack) {
		
	}
	
	protected function marshalRolodex($id, $stack) {
		
	}
	
}

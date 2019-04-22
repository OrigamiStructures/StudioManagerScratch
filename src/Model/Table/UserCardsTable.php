<?php

namespace App\Model\Table;

use App\Model\Table\ArtistCardsTable;

/**
 * CakePHP OrganizationCardsTable
 * @author dondrake
 */
class UserCardsTable extends ArtistCardsTable {
		
	public function initialize(array $config) {
	    $this->addLayerTable(['Users', 'Artists']);
		$this->stackSchema[] = ['name' => 'user',	'specs' => ['type' => 'layer']];
		$this->stackSchema[] = ['name' => 'managed_artist',	'specs' => ['type' => 'layer']];
		$this->seedPoints = array_merge(
				$this->seedPoints, 
				[
					'user', 'users',
					'managed_artist', 'managed_artists'
				]);
		parent::initialize($config);
	}
	
	/**
	 * The rolodex cards for a user ? :
	 *		Any contacts
	 *		Any managed artists
	 *		Any managers
	 * 
	 * @param type $ids
	 */
	protected function distillFromUsers($ids) {
		
	}
	
	protected function distillFromManagedAritsts($ids) {
		
	}
	
	protected function marshalUser($id, $stack) {
		
	}
	
	protected function marshalManagedArtist($id, $stack) {
		
	}
	
}

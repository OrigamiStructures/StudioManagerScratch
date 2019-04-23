<?php

namespace App\Model\Table;

use App\Model\Table\PersonCardsTable;

/**
 * CakePHP OrganizationCardsTable
 * @author dondrake
 */
class ArtistCardsTable extends PersonCardsTable {
		
	public function initialize(array $config) {
	    $this->addLayerTable(['Artists', 'Managers']);
		$this->addStackSchema(['artist', 'manager']);
		$this->addSeedPoint([
					'artist', 'artists',
					'manager', 'managers'
				]);
		parent::initialize($config);
	}
	
	protected function distillFromArtists($ids) {
		
	}
	
	protected function distillFromManagers($ids) {
		
	}
	
	protected function marshalArtist($id, $stack) {
		
	}
	
	protected function marshalManager($id, $stack) {
		
	}
	
}

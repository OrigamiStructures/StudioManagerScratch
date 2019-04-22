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
		$this->stackSchema[] = ['name' => 'artist',	'specs' => ['type' => 'layer']];
		$this->seedPoints = array_merge(
				$this->seedPoints, 
				[
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

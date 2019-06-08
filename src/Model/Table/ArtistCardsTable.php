<?php
namespace App\Model\Table;

use App\Model\Table\PersonCardsTable;

/**
 * CakePHP ArtistCardsTable
 * 
 * @todo When is this enstantiated? Note below:
 * There is more speculation below, but it seems obvious at this 
 * moment that these are made when a manager needs to work with 
 * or for their artist. Or when a registered user needs to 
 * act as themselves as an artist.
 * 
 * The name of this card implies it will only be made if the Identity 
 * also appears as the member_id of an Artist record. There is no code 
 * in the distillers or marshaller to guarantee this fact.
 * 
 * If the RolodexCardsTable is a factory it is possible that it will 
 * take steps during after distillation to enhance the root ID set to 
 * make full determination of the table types required for each stack. 
 * I don't really understand the use patterns enough to know how to 
 * proceed at this point.
 * 
 * @author dondrake
 */
class ArtistCardsTable extends PersonCardsTable {
		
	public function initialize(array $config) {
		parent::initialize($config);
	    $this->addLayerTable(['Manifests', 'Artworks']);
		$this->addStackSchema(['manifest', 'managers', 'artworks']);
		$this->addSeedPoint([
					'manifest', 'manifests',
					'artwork', 'artworks',
					'manager', 'managers'
				]);
	}
	
	protected function distillFromManifest($ids) {
		$IDs = $this->Manifests->find('list', ['valueField' => 'member_id'])
				->where(['id IN' => $ids])
				->toArray();
		return array_unique($IDs);
	}
	
	protected function distillFromArtwork($ids) {
		$IDs = $this->Artworks->find('list', ['valueField' => 'artist_id'])
				->where(['id IN' => $ids])
				->toArray();
		return array_unique($IDs);
	}
	
	/**
	 * 
	 * 
	 * @param array $ids
	 * @return array
	 */
	protected function distillFromManager($ids) {
		$IDs = $this->Manifests->find('list', ['valueField' => 'member_id'])
				->find('managedBy', ['ids' => $ids])
//				->where(['manager_id IN' => $ids])
				->toArray();
		return array_unique($IDs);
	}
	
	protected function marshalArtworks($ids, $stack) {
		if ($stack->count('identity')) {
			$artworks = $this->Artworks->find('all')
					->where(['artist_id' => $stack->rootId()]);
			$stack->set(['artworks' => $artworks->toArray()]);
		}		
		return $stack;
	}
	
	protected function marshalManifest($id, $stack) {
		if ($stack->count('identity')) {
			$image = $this->Manifests->find('all')
					->where(['member_id' => $stack->rootId()]);
			$stack->set(['manifest' => $image->toArray()]);
		}		
		return $stack;
	}
	
	/**
	 * Get the permitted Manager hook data
	 * 
	 * @todo This is the point we should honor Artist permission settings
	 * 
	 * @todo make a map_reducer?
	 * 
	 * @param string $id
	 * @param StackEntity $stack
	 * @return StackEntity
	 */
	protected function marshalManagers($id, $stack) {
		if ($stack->count('identity')) {
			$managerIds = $stack->manifest->valueList('manager_id');
			$dataOwner = $this->associations()->get('DataOwners')
				->find(
					'hook', 
					['contain' => [
						'Members' =>[
							'fields' => [
								'Members.first_name',
								'Members.last_name'
							]
						]
					]]
				)
				->where(['DataOwners.id IN' => $managerIds])
				->toArray();
			
			$stack->set(['managers' => $dataOwner]);			
		}		
		return $stack;
	}
	
}

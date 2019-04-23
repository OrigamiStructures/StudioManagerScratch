<?php
namespace App\Model\Table;

use App\Model\Table\PersonCardsTable;

/**
 * CakePHP ArtistCardsTable
 * 
 * @todo When is this enstantiated? Note below:
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
	    $this->addLayerTable(['Artists']);
		$this->stackSchema[] = ['name' => 'artists',	'specs' => ['type' => 'layer']];
		$this->stackSchema[] = ['name' => 'managers',	'specs' => ['type' => 'layer']];
		$this->seedPoints = array_merge(
				$this->seedPoints, 
				[
					'artist', 'artists',
					'manager', 'managers'
				]);
	}
	
	protected function distillFromArtist($ids) {
		$IDs = $this->Artists->find('list', ['valueField' => 'member_id'])
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
		$IDs = $this->Artists->find('list', ['valueField' => 'member_id'])
				->where(['manager_id IN' => $ids])
				->toArray();
		return array_unique($IDs);
	}
	
	protected function marshalArtists($id, $stack) {
		if ($stack->count('identity')) {
			$image = $this->Artists->find('all')
					->where(['member_id' => $stack->rootId()]);
			$stack->set(['artists' => $image->toArray()]);
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
			$managerIds = $stack->artists->valueList('manager_id');
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

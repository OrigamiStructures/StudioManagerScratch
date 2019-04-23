<?php
namespace App\Model\Table;

use App\Model\Table\PersonCardsTable;

/**
 * CakePHP OrganizationCardsTable
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
	 * @todo This needs to account for publishing settings in the record. 
	 * It may be that we have to check to see the current user and filter 
	 * the find based on their relationship to the record and the publish 
	 * settings, or we may grab everything and filter it at a later point. 
	 * But the data MUST be filtered because we don't want an API delivery 
	 * to expose data it shouldn't. It may actually be too early to filter 
	 * at this point. We are, after all, only working out which stacks are 
	 * required to support these id'd artists and NOT actually getting 
	 * data for direct display. These id's are assumed to have come through 
	 * a legitimate process and need context.
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
//		osd($stack);
//		osd($stack->rootElement());die;
		if ($stack->count('identity')) {
			$image = $this->Artists->find('all')
					->where(['manager_id' => $stack->dataOwner()])
					->contain(['Members']);
			$stack->set(['image' => $image->toArray()]);
		}		
		return $stack;
	}
	
	/**
	 * Get the permitted Manager hook data
	 * 
	 * @todo This is the point we should honor Artist permission settings
	 * 
	 * @param string $id
	 * @param StackEntity $stack
	 * @return StackEntity
	 */
	protected function marshalManagers($id, $stack) {
//		if ($stack->count('identity')) {
//			$image = $this->Images->find('all')
//					->where(['id' => $stack->rootElement()->imageId()]);
//			$stack->set(['image' => $image->toArray()]);
//		}		
		return $stack;
	}
	
}

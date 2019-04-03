<?php
namespace App\Model\Table;

use App\Model\Table\RolodexCardsTable;

/**
 * Description of CategoryCardsTable
 *
 * @author dondrake
 */
class CategoryCardsTable extends RolodexCardsTable
{
	
	public function initialize(array $config) {
		$this->layerTables[] = 'Members';
		$this->stackSchema[] = ['name' => 'members',	'specs' => ['type' => 'layer']];
		$this->seedPoints = array_merge($this->seedPoints, ['member', 'members']);
		parent::initialize($config);
	}
	
	protected function loadFromMember($ids) {
		$records = $this->GroupsMembers
			->find('all')
			->where(['member_id IN' => $ids]);
		$joins = collection($records);
		$IDs = $joins->reduce(function($accum, $entity, $index){
			$accum[] = $entity->group_id;
			return $accum;
		}, []);
		return $this->stacksFromIdentities($IDs);
	}
	
}

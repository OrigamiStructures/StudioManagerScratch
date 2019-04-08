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
	
	protected function marshalMembers($id, $stack) {
		if ($stack->count('identity')) {
            $records = $this->GroupsMembers
                ->find('all')
                ->where(['group_id' => $id])
                ->toArray();
			
            $joins = collection($records);
            $IDs = $joins->reduce(function($accum, $entity, $index){
                $accum[] = $entity->member_id;
                return $accum;
            }, []);
            $stack = $this->addMembers($IDs, $stack);
		}
		return $stack;
	}
    
    private function addMembers($IDs, $stack) {
        if(empty($IDs)) {
            $stack->set(['members' => []]);
        } else {
            $members = $this->Members
                ->find('hook')
                ->where(['id IN' => $IDs])
                ;
            $members = $members->toArray();
            $stack->set(['members' => $members]);
        }
        return $stack;
    }
	
}

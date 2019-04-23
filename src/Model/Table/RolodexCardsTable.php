<?php

namespace App\Model\Table;

use App\Model\Table\StacksTable;
use App\Model\Behavior\IntegerQueryBehavior;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\Utility\Hash;
use App\Model\Lib\StackSet;
use Cake\Cache\Cache;


/**
 * Members Model
 *
 */
class RolodexCardsTable extends StacksTable {
	
	/**
	 * {@inheritdoc}
	 */
	protected $rootName = 'identity';
	
	/**
	 * {@inheritdoc}
	 */
	protected $rootDisplaySource = 'name';

    protected $seedPoints = [
		'identity', 
		'identities',
		'data_owner', 
		'data_owners',
		'membership', 
		'memberships'
	];
	
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config) {
        $this->setTable('members');
		$this->_initializeAssociations();
        $this->addLayerTable(['Identities', 'GroupsMembers']);
        $this->addStackSchema(['identity', 'data_owner', 'memberships']);
		parent::initialize($config);
	}

	protected function _initializeAssociations() {
		// also see $this::layerTables setup by StackTable
		$this->belongsTo('DataOwners')
			->setProperty('dataOwner')
			->setForeignKey('user_id')
			->setFinder('hook')
			;
        $this->belongsToMany(
			'Memberships', 
			['joinTable' => 'groups_members'])
			->setForeignKey('member_id')
			->setTargetForeignKey('group_id')
			->setProperty('memberships')
			->setFinder('hook')
			;
	}

    protected function _initializeBehaviors() {
        $this->addBehavior('IntegerQuery');
        $this->addBehavior('Timestamp');
    }
	
	/**
	 * By id or array of IDs
	 * 
	 * @param \App\Model\Table\Query $query
	 * @param array $options
	 * @return array
	 */
	public function findRolodexCards(Query $query, $options) {
        return $this->integer($query, 'id', $options['values']);
			}
		
	/**
	 * Load the artwork stacks to support these artworks
	 * 
	 * @param array $ids Artwork ids
	 * @return StackSet
	     */
	protected function distillFromIdentity($ids) {
		return $ids;
	}
	
	protected function distillFromMembership($ids) {
		$records = $this->GroupsMembers
			->find('all')
			->where(['group_id IN' => $ids]);
		$joins = collection($records);
		$IDs = $joins->reduce(function($accum, $entity, $index){
			$accum[] = $entity->member_id;
			return $accum;
		}, []);
		return $IDs;
	}
	
	protected function distillFromDataOwner($ids) {
		$records = $this->Identities
				->find('all')
				->select(['id', 'user_id'])
				->where(['user_id IN' => $ids]);
		$IDs = collection($records)
				->reduce(function($accum, $entity, $index){
					$accum[] = $entity->id;
					return $accum;
				}, []);
		return $IDs;
	}
		
	protected function marshalIdentity($id, $stack) {
			$identity = $this->Identities
                ->find('all')
                ->where(['id' => $id]);
			$stack->set(['identity' => $identity->toArray()]);
			return $stack;
	}
	
	protected function marshalDataOwner($id, $stack) {
		if ($stack->count('identity')) {
			$dataOwner = $this->associations()->get('DataOwners')
					->find('hook')
					->where(['id' => $stack->identity->element(0)->user_id]);
			$stack->set(['data_owner' => $dataOwner->toArray()]);
		}
		return $stack;
	}
	
	protected function marshalMemberships($id, $stack) {
		if ($stack->count('identity')) {
            $records = $this->GroupsMembers
                ->find('all')
                ->where(['member_id' => $id])
                ->toArray();
            $joins = collection($records);
            $IDs = $joins->reduce(function($accum, $entity, $index){
                $accum[] = $entity->group_id;
                return $accum;
            }, []);
            $stack = $this->addMemberships($IDs, $stack);
		}
		return $stack;
	}
    
    private function addMemberships($IDs, $stack) {
        if(empty($IDs)) {
            $stack->set(['memberships' => []]);
        } else {
            $memberships = $this->_associations->get('Memberships')
                ->find('hook')
                ->where(['id IN' => $IDs])
                ;
            $stack->set(['memberships' => $memberships->toArray()]);
        }
        return $stack;
    }

}

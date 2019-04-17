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
	protected $root = 'identity';

	protected $stackSchema = 	[
            ['name' => 'identity',		'specs' => ['type' => 'layer']],
            ['name' => 'data_owner',	'specs' => ['type' => 'layer']],
            ['name' => 'memberships',	'specs' => ['type' => 'layer']],
        ];
	
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
		return $this->stacksFromIdentities($ids);
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
		return $this->stacksFromIdentities($IDs);
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
		return $this->stacksFromIdentities($IDs);
	}

	/**
	 * Read the stack from cache or assemble it and cache it
	 * 
	 * This is the destination for all the distillFrom variants. 
	 * They work to derive the member_id values required to 
	 * run this stack building process
	 * 
	 * There will be other marshalling methods added by the 
	 * various sub classes. Each sub class holds its own. 
	 * They are all named by the column names listed in the 
	 * schema defined in the table. 
	 * 
	 * @param array $ids Member ids
	 * @return StackSet
	 */
    protected function stacksFromIdentities($ids) {
		$this->stacks = new StackSet();
        foreach ($ids as $id) {
			$stack = FALSE;
			if (!$stack && !$this->stacks->isMember($id)) {
				$stack = $this->marshalStack($id);
			}
			if ($stack->count('identity')) {
				$stack->clean();
				$this->stacks->insert($id, $stack);
			}       
		}
		return $this->stacks;
	}
	
	protected function marshalStack($id) {

		$layers = Hash::extract($this->stackSchema, '{n}.name');
		$stack = $this->newEntity([]);
		foreach($layers as $layer) {
			$method = 'marshal'.ucfirst($layer);
			$stack = $this->$method($id, $stack);
		}
		return $stack;
	}
	
	protected function marshalIdentity($id, $stack) {
			$identity = $this->Identities
                ->find('all')
                ->where(['id' => $id]);
			$stack->set(['identity' => $identity->toArray()]);
			return $stack;
	}
	
	protected function marshalData_owner($id, $stack) {
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

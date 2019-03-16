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

    protected $layerTables = ['Identities'];

	protected $stackSchema = 	[
			['name' => 'identity',		'specs' => ['type' => 'layer']],
            ['name' => 'data_owner',		'specs' => ['type' => 'layer']],
            ['name' => 'memberships',		'specs' => ['type' => 'layer']],
        ];
	
    protected $seedPoints = [
			'identity', 
			'data_owner', 
			'memberships', 
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
		parent::initialize($config);
	}

	protected function _initializeAssociations() {
		$this->belongsTo('DataOwner')
			->setProperty('dataOwner')
			->setForeignKey('user_id')
			->setFinder('hook')
			;
        $this->belongsToMany(
			'Memberships', 
			['joinTable' => 'groups_members'])
			->setTargetForeignKey('group_id')
			->setForeignKey('member_id')
			->setFinder('hook')
			;
	}

    protected function _initializeBehaviors() {
        $this->addBehavior('IntegerQuery');
        $this->addBehavior('Timestamp');
    }
	
	public function findRolodexCards(Query $query, $options) {
        return $this->integer($query, 'id', $options['values']);
			}
	
			
	/**
	 * Load the artwork stacks to support these artworks
	 * 
	 * @param array $ids Artwork ids
	 * @return StackSet
	     */
	protected function loadFromIdentity($ids) {
		return $this->stacksFromIdentities($ids);
	}
	
	/**
	 * Read the stack from cache or assemble it and cache it
	 * 
	 * This is an alternate finder for cases where you have a set 
	 * of Members id. 
	 * 
	 * @param array $ids Member ids
	 * @return StackSet
	 */
    public function stacksFromIdentities($ids) {
        if (!is_array($ids)) {
            $msg = "The ids must be provided as an array.";
            throw new \BadMethodCallException($msg);
        }
		$this->stacks = new StackSet();
        foreach ($ids as $id) {
			$stack = FALSE;
//            $stack = Cache::read(cacheTools::key($id), cacheTools::config());
			if (!$stack && !$this->stacks->isMember($id)) {
				$stack = $this->marshalStack($id);
			}
			if ($stack->count('member')) {
				$stack->clean();
				$this->stacks->insert($id, $stack);
			}       
		}
		return $this->stacks;
	}
		
	protected function marshalStack($id) {

		$layers = Hash::extract($this->stackSchema, '{n}.path');
		$stack = $this->newEntity([]);
		foreach($layers as $layer) {
			$method = 'marshal'.ucfirst($layer);
			$stack = $this->$method($id, $stack);
		}
		return $stack;
	}
	
	protected function marshalIdentity($id, $stack) {
			$identity = $this->Identity->find('identity', ['values' => [$id]]);
			$stack->set(['identity' => $identity->toArray()]);
	}
	
	protected function marshalDataOwner($id, $stack) {
		if ($stack->count('member')) {
			$dataOwner = $this->DataOwners->find('hook')->where(['id' => $stack->dataOwner()]);
			$stack->set(['$dataOwner' => $dataOwner->toArray()]);
		}
	}
	
	protected function marshalMemberships($id, $stacks) {
		if ($stack->count('member')) {
			$memberships = $this->Memberships->find('hook')->where(['member_id' => $id]);
			$stack->set(['memberships' => $memberships->toArray()]);
		}
	}
	
	
	function hold(){
		if (!$stack && !$this->stacks->isMember($id)) {

//			$member = $this->Members->find('members', ['values' => [$id]]);
//				$stack->set(['member' => $member->toArray()]);

			if ($stack->count('member')) {
				// First do the simple finds
				$contacts = $this->Contacts->find('inMembers', ['values' => [$id]]);
				$addresses = $this->Addresses->find('inMembers', ['values' => [$id]]);
				$group = $this->Groups->find('inMembers', ['values' => [$id]]);
				$stack->set([
					'contacts' => $contacts->toArray(),
					'addresses' => $addresses->toArray(),
					'group' => $group->toArray(),
					]);

				//Burrow through and set up the Members for the Groups that contain this
				$memberships = $this->lookupMemberships($id);
				$stack->set(['member_of' => $memberships]);

				//Burrow through and set up the members of this group
				$group_members = $this->lookupGroupMembers($id, $stack);
				$stack->set(['has_members' => ((is_array($group_members)) ? 
					$group_members : $group_members->toArray())]);

			}
		}
			
    }

}

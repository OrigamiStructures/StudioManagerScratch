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
		// also see $this::layerTables
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
			->setProperty('memberships')
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
			$identity = $this->Identities->find('all')->where(['id' => $id]);
			$stack->set(['identity' => $identity->toArray()]);
			return $stack;
	}
	
	protected function marshalData_owner($id, $stack) {
		if ($stack->count('identity')) {
			$dataOwner = $this->_associations->get('DataOwner')
					->find('hook')
					->where(['id' => $stack->identity->element(0)->user_id]);
			$stack->set(['$dataOwner' => $dataOwner->toArray()]);
		}
		return $stack;
	}
	
	protected function marshalMemberships($id, $stack) {
//		osd($this->_associations->get('Memberships'));die;
//		osd($this->associations());die;
		if ($stack->count('identity')) {
			$memberships = $this->_associations->get('Memberships')
					->find('hook')
					->where(['member_id' => $id]);
			$stack->set(['memberships' => $memberships->toArray()]);
		}
		return $stack;
	}

}

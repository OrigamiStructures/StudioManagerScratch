<?php

namespace App\Model\Table;

use App\Model\Entity\StackEntity;
use App\Model\Table\StacksTable;
use App\Model\Behavior\IntegerQueryBehavior;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\Utility\Hash;
use App\Model\Lib\StackSet;
use Cake\Cache\Cache;
use Cake\Core\Configure;

/**
 * Members Model
 *
 */
class RolodexCardsTable extends StacksTable {

//	use ManagementTrait;

	/**
	 * {@inheritdoc}
	 */
	protected $rootName = 'identity';

	protected $rootTable = 'Identities';

	/**
	 * {@inheritdoc}
	 */
	public $rootDisplaySource = 'name';

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config) {
        $this->setTable('members');
		$this->_initializeAssociations();
        $this->addLayerTable(['Identities', 'GroupsMembers', 'Users', 'Shares']);
        $this->addStackSchema(['identity', 'data_owner', 'memberships', 'shares']);
        $this->addSeedPoint([
            'identity',
            'identities',
            'data_owner',
            'data_owners',
            'membership',
            'memberships',
			'manager',
			'managers',
			'supervisor',
			'supervisors',
//            'shared',
//            'share',
            'shares'
        ]);
		parent::initialize($config);
	}

	protected function _initializeAssociations() {
		// also see $this::layerTables setup by StackTable
		$this->belongsTo('DataOwners')
			->setProperty('dataOwnerId')
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
	 *
	 *
	 * @param array $ids
	 * @return StackSet
	     */
	protected function distillFromIdentity($ids) {
		return $this->Identities->find('all')->where(['id IN' => $ids]);
	}

	/**
	 * @todo Is there a more direct query that could be built here?
	 *
	 * @param array $ids
	 * @return Query
	 */
	protected function distillFromMembership($ids) {
		$records = $this->GroupsMembers
			->find('all')
			->where(['group_id IN' => $ids]);
		$joins = collection($records);
		$IDs = $joins->reduce(function($accum, $entity, $index){
			$accum[] = $entity->member_id;
			return $accum;
		}, []);
		return $this->distillFromIdentity($IDs);
	}

	/**
	 * @todo Is this a valid seed point? Who/How used?
	 *
	 * @param array $ids
	 * @return Query
	 */
	protected function distillFromDataOwner($ids) {
		return $this->Identities
				->find('all')
				->select(['id', 'user_id'])
				->where(['user_id IN' => $ids]);
	}

	protected function distillFromManager($ids) {
		$records = $this->Users
				->find('all')
				->select(['id', 'management_token', 'member_id'])
				->where(['management_token IN' => $ids]);
		$IDs = collection($records)
				->reduce(function($accum, $entity, $index){
					$accum[] = $entity->member_id;
					return $accum;
				}, []);
		return $this->distillFromIdentity($IDs);
	}

	protected function distillFromSupervisor($ids) {
		return $this->distillFromManager($ids);
	}

    protected function distillFromShare($ids)
    {
        $records = $this->Shares
            ->find('all')
            ->where(['OR' => [
                'supervisor_id IN' => $ids,
                'manager_id IN' => $ids,
                'category_id IN' => $ids
            ]]);
        $IDs = collection($records)
            ->reduce(function($accum, $entity, $index){
                $accum = array_merge($accum, [
                    $entity->supervisor_id,
                    $entity->manager_id,
                    $entity->category_id
                ]);
                return $accum;
            }, []);
        return $this->distillFromIdentity($IDs);
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
					->where(['id' => $stack->dataOwnerId()]);
			$stack->set(['data_owner' => $dataOwner->toArray()]);
		}
		return $stack;
	}

    protected function marshalShares($id, $stack)
    {
        /* @var StackEntity $stack */

        if ($stack->count('identity')) {
            $query = $this->Shares
                ->find('all')
                ->where(['OR' => [
                    'Shares.supervisor_id' => $stack->rootID(),
                    'Shares.manager_id' => $stack->rootID(),
                    'Shares.category_id' => $stack->rootID()
                ]]);
            $shares = $this->Shares->hydrateLayer($query);
        }
        $stack->set(['shares' => $shares]);
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

	protected function writeCache($id, $stack) {
		if (Configure::read('rolodexCache')) {
			$result = parent::writeCache($id, $stack);
		} else {
			$result = FALSE;
		}
		return $result;
	}

	protected function readCache($id) {
		if (Configure::read('rolodexCache')) {
			$result = parent::readCache($id, $stack);
		} else {
			$result = FALSE;
		}
		return $result;
	}

}

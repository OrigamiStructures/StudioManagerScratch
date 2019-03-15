<?php

namespace App\Model\Table;

use App\Model\Table\StacksTable;
use App\Model\Behavior\IntegerQueryBehavior;
use Cake\ORM\Behavior\TimestampBehavior;


/**
 * Members Model
 *
 */
class RolodexCardsTable extends StacksTable {

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config) {
		parent::initialize($config);
		$this->setTable('members');
		$this->_initializeAssociations();
	}

	protected function _initializeAssociations() {
		$this->belongsTo('DataOwner')
			->setProperty('dataOwner')
			->setForeignKey('user_id')
			->setFinder('hook')
			;
		
        $this->belongsToMany('Memberships', [
            'className' => 'Groups',
            'foreignKey' => 'member_id',
            'targetForeignKey' => 'group_id',
            'joinTable' => 'groups_members'
        ]);
		
//		$this->hasMany('Memberships')
//				->setProperty('memberships')
//				->setForeignKey('member_id')
//				->setFinder('hook');
//			'dependent' => TRUE,
//		]);
	}

    protected function _initializeBehaviors() {
        $this->addBehavior('IntegerQuery');
        $this->addBehavior('Timestamp');
    }
	
	public function findRolodexCards(Query $query, $options) {
        return $this->integer($query, 'id', $options['values']);
	}
	
	public function findOwner(Query $query, $options) {
		$query->select(['id', 'username']);
	}

}

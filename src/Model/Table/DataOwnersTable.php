<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\Table;

use Cake\ORM\Query;
use App\Model\Table\AppTable;

/**
 * CakePHP DataOwner
 * @author dondrake
 */
class DataOwnersTable extends AppTable {
	
	public function initialize(array $config) {
		$this->setTable('users');
		parent::initialize($config);
		$this->belongsTo('Members', [
            'foreignKey' => 'member_id'
        ]);
	}
	
	/**
	 * Association finder to get minimal data points
	 * 
	 * @param Query $query
	 * @param array $options
	 * @return query
	 */
	public function findHook(Query $query, $options) {
		return $query->select(['DataOwners.id', 'DataOwners.username', 'DataOwners.member_id']);
	}
	
}

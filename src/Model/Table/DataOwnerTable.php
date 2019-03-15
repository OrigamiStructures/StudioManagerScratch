<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;

/**
 * CakePHP DataOwner
 * @author dondrake
 */
class DataOwnerTable extends Table {
	
	public function initialize(array $config) {
		$this->setTable('users');
		parent::initialize($config);
	}
	
	/**
	 * Association finder to get minimal data points
	 * 
	 * @param Query $query
	 * @param array $options
	 * @return query
	 */
	public function findHook(Query $query, $options) {
		return $query->select(['id', 'username']);
	}
	
}

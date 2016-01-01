<?php

namespace App\Model\Table;

use Cake\ORM\Locator\TableLocator;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CSTableLocator
 *
 * @author dondrake
 */
class CSTableLocator extends TableLocator {
	
	public $SystemState;
	
	public function __construct($SystemState) {
		$this->SystemState = $SystemState;
	}


	public function get($alias, array $options = []) {
		return parent::get($alias, ['SystemState' => $this->SystemState]);
	}
	
}

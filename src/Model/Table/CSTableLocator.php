<?php

namespace App\Model\Table;

use Cake\ORM\Locator\TableLocator;

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

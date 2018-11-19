<?php
namespace App\Model\Lib;

/**
 * Description of Stacks
 *
 * @author dondrake
 */
class StackSet {
	
	protected $_stacks = [];

	public function insert($id, $stack) {
		$this->_stacks[$id] = $stack;
	}
	
	public function all() {
		return $this->_stacks;
	}

}

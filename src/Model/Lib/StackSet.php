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
	
	public function members() {
		return array_keys($this->_stacks);
	}
	
	public function isMember($id) {
		return array_key_exists($id, $this->_stacks);
	}
	
	public function load($layer, $options = []){
		$results = [];
		foreach($this->_stacks as $stack) {
			$results = array_merge($results, $stack->load($layer, $options));
		}
		return $results;
	}
	
	public function owner($layer, $id) {
		$stacks = [];
		foreach ($this->_stacks as $stack) {
			if ($stack->exists($layer, $id)) {
				$stacks[] = $stack;
			}
		}
		return $stacks;
	}
	
	public function IDs($layer) {
		$ids = [];
		foreach($this->_stacks as $stack) {
			$ids = array_merge($ids, $stack->IDs($layer));
		}
		return $ids;
	}

}

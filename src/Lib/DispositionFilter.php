<?php
namespace App\Lib;

use App\Lib\Traits\DispositionFilterTrait;

/**
 * DispositionFilter
 *
 * @author dondrake
 */
class DispositionFilter {
	
	use DispositionFilterTrait;
	
	protected $_filters = [];

	/**
	 * Add a disposition filter to the set
	 * 
	 * @param callable $filter
	 */
	public function addFilter($filter) {
		if (method_exists($this, $filter) || is_callable($filter)) {
			$this->_filters[] = $filter;
			return $this;
		}
		throw new \BadFunctionCallException('Argument must be the name of a filter method or a callable.');
	}
	
	/**
	 * Run the dispostion through the set of filters
	 * 
	 * @param entity $disposition
	 * @param string $key
	 * @return boolean
	 */
	public function runFilter($disposition, $key) {
		$result = TRUE;
		$index = 0;
		$limit = count($this->_filters);
		while ($result && $index < $limit) {
			$this->{$this->_filters[$index++]}($disposition, $key);
		}
		return $result;
	}
}

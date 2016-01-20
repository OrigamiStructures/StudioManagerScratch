<?php
namespace App\View\Helper;

use Cake\View\Helper;

/**
 * FactoryHelper swaps Edition and Format helper subclasses onto a common call point
 * 
 * Edition and Format display and form output follow a variety of rules 
 * depending on the Edition type. All the view and fieldset elements are 
 * standardized and they all call a single helper variable for service. So the 
 * underlying helper class must be managed so the correct rule set is used.
 * @author dondrake
 */
class FactoryHelper extends Helper {
	
	protected $_map = [
		'OpenEdition' => 'Edition',
		'LimitedEdition' => 'Edition',
		'Rights' => 'UniqueEdition',
		'Portfolio' => 'Edition',
		'Unique' => 'UniqueEdition',
	];
	
	public function load($type) {
		$version = str_replace(' ', '', $type);
		return $this->_View->loadHelper($this->_map[$version]);
	}
	
}

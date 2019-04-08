<?php
namespace App\Lib;

//use Cake\View\Helper;

/**
 * FactoryHelper swaps Edition and Format helper subclasses onto a common call point
 * 
 * Edition and Format display and form output follow a variety of rules 
 * depending on the Edition type. All the view and fieldset elements are 
 * standardized and they all call a single helper variable for service. So the 
 * underlying helper class must be managed so the correct rule set is used.
 * 
 * @author dondrake
 */
class EditionHelperFactory {
	
	protected $_View;

	/**
	 * Keyed access to concrete Edition helpers
	 * 
	 * Different edition types need different output services for 
	 * their parts. Concrete flavors of helpers are stored for each 
	 * type.
	 * 
	 * In this uninitialized state each key contains a string. 
	 * This indicates the need to load the concrete helper. The string 
	 * is the key to _concrete_build_map which tells which 
	 * edition types share flavors of concrete helper.
	 * 
	 * Once initialization is done for a specific type (and its 
	 * partners) it will hold a helper object rather than a string.
	 * 
	 * EditionFactory->concrete() is the accessor method to fetch 
	 * these helpers. That class will lazy-load the objects too.
	 *
	 * @var array
	 */
	protected $_concrete_helper = [
		EDITION_UNIQUE => 'Unique',
		EDITION_RIGHTS => 'Unique',
		
		EDITION_LIMITED => 'Editioned',
		EDITION_OPEN => 'Editioned',
		
		PORTFOLIO_LIMITED => 'Packaged',
		PORTFOLIO_OPEN => 'Packaged',
		PUBLICATION_LIMITED => 'Packaged',
		PUBLICATION_OPEN => 'Packaged',
	];
	
	/**
	 * Maps concrete helper classes to edition types
	 * 
	 * This map is used by EditionFactory::concrete() to lazy-load 
	 * Helper classes into EditionFactory::_concrete_helper keys
	 *
	 * @var array
	 */
	protected $_concrete_build_map = [
		'Unique' => [EDITION_UNIQUE, EDITION_RIGHTS],
		'Editioned' => [EDITION_LIMITED, EDITION_OPEN],
		'Packaged' => [PORTFOLIO_LIMITED, PORTFOLIO_OPEN, 
			PUBLICATION_LIMITED, PUBLICATION_OPEN]
	];

	public function __construct(\Cake\View\View $View) {
		$this->_View = $View;
		$this->SystemState = $View->SystemState;
	}
	
	/**
	 * Return the helper for a specific edition type
	 * 
	 * Lazy-load the helpers as necessary. In truth, once a particular 
	 * helper is instantiated, all edition types that use that helper 
	 * will be loaded with a reference to it. 
	 * 
	 * @param string $type An edition type
	 * @return Helper The helper that services the edition type
	 */
	public function concrete($type) {
		if (is_string($this->_concrete_helper[$type])) {
			$helper = $this->_View->loadHelper($this->_concrete_helper[$type]);
			
			foreach ($this->_concrete_build_map[$this->_concrete_helper[$type]] as $property) {
				$this->_concrete_helper[$property] = $helper;
			}
		}
		return $this->_concrete_helper[$type];
	}
}

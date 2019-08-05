<?php
namespace App\Model\Lib;

use Cake\Core\ObjectRegistry;
use App\Exception\MissingClassException;

/**
 * PersonCardRegistry
 * 
 * PersonCards are frequently used objects and appear as properties of 
 * several Stack types. This means that the same card may be needed in 
 * more than one object during a single Request/Response cycle. In such 
 * a case we need to insure all card data stays the same we will use this 
 * registry to maintain singletons and pass along references.
 * 
 * Registry operations are woven into the PersonCardsTable stack retrieval 
 * processes. This makes use of this registry (and caching) automatic. 
 * Just PersonCardsTable::find('stacksFor', [ ]) as you would for any 
 * request and you'll get a singleton based reference back (or a set of them). 
 * 
 * @author dondrake
 */
class PersonCardRegistry extends ObjectRegistry {
	
	protected function _create($class, $alias, $config) {
		
	}

	protected function _resolveClassName($class) {
		
	}

	protected function _throwMissingClassError($class, $plugin) {
		$msg = [];
		throw new MissingClassException(implode('', $msg));
	}
	
	public function load($objectName, $config = []) {
		 
	 }

}

<?php
namespace App\Lib;

use Cake\Cache\Cache;
use Cake\Core\Configure;
/**
 * CacheManager
 *
 * @author dondrake
 */
class CacheManager {

	protected $_configs;
	
	static public function clearGroups($context) {
		Configure::load('cache');
		$this->_configs = Configure::read('Cache');
		foreach (array_keys($this->_configs) as $name => $config) {
			if ($name == 'managed cache' && isset($config['groups'])) { // desig n boolean here
				// run $Name::name() 
			}
		}
	}
	
}

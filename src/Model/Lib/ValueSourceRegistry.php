<?php
namespace App\Model\Lib;

use Cake\Core\ObjectRegistry;
use App\Exception\MissingClassException;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ValueSourceRegistry
 *
 * @author dondrake
 */
class ValueSourceRegistry extends ObjectRegistry {
	
	protected function _create($class, $alias, $config) {
		osd(func_get_args());die;
		return new $class($config);
	}

	protected function _resolveClassName($class) {
		return "\\App\\Model\\Lib\\$class";
	}

	protected function _throwMissingClassError($class, $plugin) {
		$msg = [];
		$msg[] = "The class $class ";
		$msg[] = !empty($plugin) ? "for plugin '$plugin' " : '';
		$msg[] = 'is missing. Needed by ValueSourceRegistry.';
		
		throw new MissingClassException(implode('', $msg));
	}
	
	 public function load($objectName, $config = []) {
		 $config['className'] = 'ValueSource';
		 return parent::load($objectName, $config);
	 }

}

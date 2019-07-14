<?php

namespace App\Model\Table;

use Cake\ORM\Locator\TableLocator;

/**
 * CSTableLocator
 * 
 * Extends the standard TableLocator class to allow universal injection 
 * of User data. This data is the basis of determinining record ownership, 
 * supervisor/manager arrangements and shared data permissions for 
 * managers.
 *
 * @author dondrake
 */
class CSTableLocator extends TableLocator {
	
	
	public $commonConfig;
	
	/**
	 * Pass in the config values all Tables must recieve
	 * 
	 * @param array $config
	 */
	public function __construct($config) {
		$this->commonConfig = $config;
	}

	/**
	 * Merge common config values into the requested Table options
	 * 
	 * This will insure availability of the required data in the tables 
	 * but allow the overriding that data if necessary.
	 * 
	 * @todo establish an interface for the require-data objects 
	 *		store in commonConfig, then insure any overrides that are 
	 *		found in $options conform to the interface
	 * 
	 * @param type $alias
	 * @param array $options
	 * @return type
	 */
	public function get($alias, array $options = []) {
		return parent::get($alias, $this->mergeOptions($options));
	}
	
	/**
	 * Add the required common options or allow $options to replace them
	 * 
	 * @param type $options
	 */
	private function mergeOptions($options) {
		foreach ($this->commonConfig as $key => $element) {
			if (!key_exists($key, $options)) {
				$options[$key] = $this->commonConfig[$key];
			} else {
				$this->validateOverride($element, $options[$key]);
			}
		}
		
		// An empty 'className' was getting injected by someone 
		// and triggering Can't Reconfigure error
		$commonKeys = array_keys($this->commonConfig);
		foreach ($options as $key => $value) {
			if (!in_array($key, $commonKeys) && empty($options[$key])) {
				unset($options[$key]);
			}
		}
		return $options;
	}
	
	/**
	 * Insure the override value is a good match
	 * 
	 * I'm not sure what kinds of data will be stored in commonConfig 
	 * so we'll have to be flexible with checking for compatible 
	 * replacements
	 * 
	 * @todo Implement Table config override validation
	 * 
	 * @param mixed $common
	 * @param mixed $override
	 */
	private function validateOverride($common, $override) {
		// string for string
		// array, all array_keys($common) are also in $override
		// is_obj($common)'s interface is implemented by $override
		// this last one makes a hell of a lot of assumptions
		
		return TRUE;
	}
	
}

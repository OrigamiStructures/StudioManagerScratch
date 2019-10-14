<?php

namespace App\Model\Table;

use Cake\ORM\Locator\TableLocator;
use Cake\ORM\Table;

/**
 * CSTableLocator
 *
 * Extends the standard TableLocator class to allow universal injection
 * of properties on the constructed Tables.
 *
 * AppController injects
 * ```
 * CurrentUser //Authorized user data object
 * ContextUser //IDs detailing users current focus (object)
 * ```
 * for all tables created through a Controller.
 *
 * Rules:
 *  - construct with keyed injections, all tables will receive them
 *  - get() with overlapping config keys, get-version will win
 *  - if requested Table already exists, that version will be returned
 *      with no added injections (throws Runtime error?)
 *
 * This data is the basis of determining record ownership,
 * supervisor/manager arrangements and shared data permissions for
 * managers.
 *
 * @todo Do the properties get injected when tables are build from
 *      non-controller locations (like other models)? (integration testing)
 * @todo Do we want the properties injected for API calls? Can this
 *      be automatically handled with url prefix features in Cake?
 *      Possibly a config list of prefixes to switch on/off injection? (business rule)
 *
 * @author dondrake
 */
class CSTableLocator extends TableLocator {


	protected $commonConfig;

	/**
	 * Pass in the config values all Tables will receive
     *
     * This Table factory will inject these stored values into every constructed Table.
     * The keys will become properties of the table and the values will be
     * stored in those properties.
	 *
	 * @param array $config Configuration values for all tables
     * @param array $locations Locations where tables should be looked for
	 */
	public function __construct($config = [], $locations = NULL) {
		$this->commonConfig = $config;
        parent::__construct($locations);
	}

	/**
	 * Merge common config values into the requested Table options
	 *
	 * This will insure availability of the required data in the tables
	 * but allow the overriding that data if necessary.
	 *
	 * @param String $alias
	 * @param array $options
	 * @return Table
	 */
	public function get($alias, array $options = []) {

		return parent::get($alias, $this->mergeOptions($options));
	}

    /**
     * Discover names of the properties that will be injected into Tables
     *
     * @return array
     */
	public function getInjectionKeys() {
	    return array_keys($this->commonConfig);
    }

    public function getInjectionValue(string $name)
    {
        return $this->commonConfig[$name] ?? NULL;
    }

    /**
     * Set a single injection property
     *
     * @param string $name
     * @param mixed $value
     */
    public function setInjection(string $name, $value)
    {
        $this->commonConfig[$name] = $value;
    }

    /**
     * Get the full array of stored injections
     *
     * @return array
     */
    public function getInjections()
    {
        return $this->commonConfig ?? [];
    }

    /**
     * Overwrite the entire set of property injections
     *
     * @param array $values
     */
    public function setInjections(array $values)
    {
        $this->commonConfig = $values;
    }

	/**
	 * Add the required common options or allow $options to replace them
	 *
	 * @param array $options
     * @return array
	 */
	private function mergeOptions($options) {
        // An empty 'className' was getting injected by someone
        // and triggering Can't Reconfigure error
        foreach ($options as $key => $value) {
            if (empty($options[$key])) {
                unset($options[$key]);
            }
        }

		foreach ($this->commonConfig as $key => $element) {
			if (!key_exists($key, $options)) {
				$options[$key] = $this->commonConfig[$key];
			}
		}
//        debug($options);
		return $options;
	}

}

<?php
namespace App\Model\Table;

use App\Lib\SystemState;
use App\Model\Lib\ContextUser;
use Cake\ORM\Table;
use Cake\Http\Session;
use App\Model\Lib\CurrentUser;

/**
 * Description of AppTable
 *
 * @author dondrake
 */
class AppTable extends Table {

    /**
     * List of valid keys for Table::__construct()
     *
     * Documentation for the Table super-class provided this list of values.
     *
     * @var array
     */
    private $standardConfigKeys = [
        'table',
        'alias',
        'connection',
        'entityClass',
        'schema',
        'eventManager',
        'behaviors',
        'associations',
        'validator',
        // additional keys that are automatic (revealed by tests)
        'className',
        'registryAlias'
    ];

    /**
     * Keys sent in config beyond the ::standardConfigKeys set
     *
     * Used in testing, this will allow detection of changes to the Core Table behavior.
     * These are the names of config properties sent by the override Table factory, CSTableLocator
     *
     * @var array
     */
    private $injectedProperties;

    /**
     * @var CurrentUser
     */
    protected $CurrentUser;

    /**
     * @var ContextUser
     */
	protected $ContextUser;

	/**
	 * Construct a Table
	 *
	 * An override Table factory allows for injection of properties beyond those
     * supported by Cake's core Table super-class. Once the Table is built, any
     * additional config values sent by the new factory will be moved onto properties
     * of this Table.
     *
     * The factory override is put in place by AppController and the basic set of
     * additional config values is set at that time. The replacement factory
     * allows changes and overrides of its configured values (App\Model\CSTableLocator)
	 *
	 * @param array $config
	 */
	public function __construct(array $config = [])
    {
        parent::__construct($config);

        foreach ($this->customConfigKeys(array_keys($config)) as $key) {
            $this->$key = $config[$key];
        }
    }

    /**
     * Discover (and record) any additional config keys
     *
     * @param $providedKeys
     * @return array
     */
    private function customConfigKeys($providedKeys)
    {
        $this->injectedProperties = array_diff($providedKeys, $this->standardConfigKeys);
        return $this->injectedProperties();
	}

    /**
     * Return the calculated list of config keys beyond the standard Cake set
     *
     * @return array
     */
    public function injectedProperties()
    {
        return $this->injectedProperties;
	}

	/**
	 * Get the currentUser object for the table
	 */
	public function currentUser() {
		return $this->CurrentUser;
	}

    /**
     * Get contextUser object
     *
     * @return ContextUser
     */
    public function contextUser() {
        return $this->ContextUser;
    }

    /**
     * Directly inject a CurrentUser object
     *
     * @param CurrentUser $userData
     */
	public function setCurrentUser($currentUser) {
		$this->CurrentUser = $currentUser;
	}

    /**
     * Directly inject a ContextUser object
     * @param ContextUser $userData
     */
	public function setContextUser($contextUser) {
		$this->ContextUser = $contextUser;
	}

    public function __debugInfo()
    {
        $debug = parent::__debugInfo();
        $debug['CurrentUser'] = $this->currentUser();
        $debug['ContextUser'] = $this->contextUser();
        $debug['SystemState'] = isset($this->SystemState) ? 'Present' : 'Not Present';

        return $debug;
	}

}

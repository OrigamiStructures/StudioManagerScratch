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
     * @var array
     */
    protected $standardConfigKeys = [
        'table',
        'alias',
        'connection',
        'entityClass',
        'schema',
        'eventManager',
        'behaviors',
        'associations',
        'validator',
        // additional keys that are automatic
        'className',
        'registryAlias'
    ];

    protected $providedConfigKeys;

    protected $CurrentUser;

    /**
     * @var ContextUser
     */
	protected $ContextUser;

	/**
	 * An override TableLocator injects config values
	 *
	 * The override is done in AppController
	 *
	 * @param array $config
	 */
	public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->providedConfigKeys = array_keys($config);
        foreach ($this->customConfigKeys() as $key) {
            $this->$key = $config[$key];
        }
    }

    protected function customConfigKeys()
    {
        return array_diff($this->providedConfigKeys, $this->standardConfigKeys);
	}

	/**
	 * Get/make the currentUser object for the table
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

        return $debug;
	}

}

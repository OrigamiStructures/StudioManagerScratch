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
	
	public $SystemState;
	
	protected $currentUser;

    /**
     * @var ContextUser
     */
	protected $contextUser;

	/**
	 * An override TableLocator injects config values
	 * 
	 * The override is done in AppController
	 * 
	 * @param array $config
	 */
	public function __construct(array $config = []){
        if (!empty($config['SystemState'])) {
            $this->SystemState = $config['SystemState'];
		}
        if (!empty($config['currentUser'])) {
            $this->currentUser = $this->setCurrentUser($config['currentUser']);
		}
		parent::__construct($config);
	}
	
	/**
	 * Get/make the currentUser object for the table
	 */
	public function currentUser() {
		return $this->currentUser;
	}

    /**
     * Get / make contextUser object
     * 
     * @return ContextUser
     */
    public function contextUser() {
        if (!isset($this->contextUser)) {
            $this->contextUser = ContextUser::instance();
        }
        return $this->contextUser;
    }
	
	public function setCurrentUser($userData) {
		$this->currentUser = $userData;
	}
	
	public function setContextUser($userData) {
		$this->contextUser = $userData;
	}
	
}

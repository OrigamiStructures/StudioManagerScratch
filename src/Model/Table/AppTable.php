<?php
namespace App\Model\Table;

use App\Lib\SystemState;
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
        if (!empty($config['contextUser'])) {
            $this->contextUser = $this->setContextUser($config['contextUser']);
		} elseif (!empty($config['currentUser']))  {
            $this->contextUser = $this->setCurrentUser($config['currentUser']);
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
	 * Get/make the currentUser object for the table
	 */
	public function contextUser() {
		return $this->contextUser;
	}
	
	public function setCurrentUser($userData) {
		$this->currentUser = $userData;
	}
	
	public function setContextUser($userData) {
		$this->contextUser = $userData;
	}
	
}

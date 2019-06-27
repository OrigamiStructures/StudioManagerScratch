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

	public function __construct(array $config = []){
        if (!empty($config['SystemState'])) {
            $this->SystemState = $config['SystemState'];
		}
        if (!empty($config['currentUser'])) {
            $this->currentUser = new CurrentUser($config['currentUser']);
		}
        if (!empty($config['contextUser'])) {
            $this->contextUser = new CurrentUser($config['contextUser']);
		} else {
            $this->contextUser = new CurrentUser($config['currentUser']);
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
	
}

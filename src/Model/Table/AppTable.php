<?php
namespace App\Model\Table;

use App\Lib\SystemState;
use Cake\ORM\Table;
use Cake\Http\Session;

/**
 * Description of AppTable
 *
 * @author dondrake
 */
class AppTable extends Table {
		
	public $SystemState;
	
	protected $session;


	public function __construct(array $config = []){
        if (!empty($config['SystemState'])) {
            $this->SystemState = $config['SystemState'];
		}
        if (!empty($config['currentUser'])) {
            $this->currentUser = $config['currentUser'];
		}
		parent::__construct($config);
	}	
	
}

<?php
namespace App\Model\Table;

use App\Lib\SystemState;
use Cake\ORM\Table;


/**
 * Description of AppTable
 *
 * @author dondrake
 */
class AppTable extends Table {
	
	public $SystemState;
	
    public function __construct(array $config = []){
		parent::__construct($config);
        if (!empty($config['SystemState'])) {
            $this->SystemState = $config['SystemState'];
        }
	}
	
	public function newId($index) {
		$id = $this->_newId($index);
		osd($id);
		return $id;
	}
	
}

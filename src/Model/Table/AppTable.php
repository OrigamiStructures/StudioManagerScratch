<?php
namespace App\Model\Table;

use App\Lib\SystemState;


/**
 * Description of AppTable
 *
 * @author dondrake
 */
class AppTable extends Table {
	
    public function __construct(array $config = []){
        if (!empty($config['SystemState'])) {
            $this->registryAlias($config['SystemState']);
        }
	}
	
	public function SystemState(ControlBloc $control) {
		$this->SystemState = $control;
	}
	
}

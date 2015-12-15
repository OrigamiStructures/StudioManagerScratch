<?php
namespace App\Model\Table;

use App\Lib\ControlBlock;


/**
 * Description of AppTable
 *
 * @author dondrake
 */
class AppTable extends Table {
	
    public function __construct(array $config = []){
        if (!empty($config['controlBlock'])) {
            $this->registryAlias($config['controlBlock']);
        }
	}
	
	public function controlBlock(ControlBloc $control) {
		$this->ControlBlock = $control;
	}
	
}

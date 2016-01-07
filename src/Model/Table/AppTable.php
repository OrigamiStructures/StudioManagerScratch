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
        if (!empty($config['SystemState'])) {
            $this->SystemState = $config['SystemState'];
		}
		parent::__construct($config);
	}
		
// <editor-fold defaultstate="collapsed" desc="These overrides didn't work with {table->get(id) calls">
//	public function belongsTo($associated, array $options = array()) {
//		return parent::belongsTo($associated, $options);
//	}
//
//
//	public function belongsToMany($associated, array $options = array()) {
//		return parent::belongsToMany($associated, $options);
//	}
//
//
//	public function hasMany($associated, array $options = array()) {
//		return parent::hasMany($associated, $options);
//	}
//
//
//	public function hasOne($associated, array $options = array()) {
//		parent::hasOne($associated, $options);
//	}

// </editor-fold>
	
}

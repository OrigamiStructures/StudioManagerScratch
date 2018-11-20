<?php
namespace App\SiteMetrics;

use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * Description of Metrics
 *
 * @author dondrake
 */
class CollectMetrics {
	
	protected $_timer;
	protected $_logConfig;
	protected static $_instance;
	protected $_timerSwitch;
	protected $_logEntry = [];

	private function __construct() {
		$this->_timerSwitch = Configure::read('timers');
		if ($this->_timerSwitch) {
			$this->_timer = new \OSDTImer(); 
		}
	}
	
	public function startLogEntry($path) {
		$identifiers = explode('.', $path);
		$this->_logEntry = Hash::insert($this->_logEntry, $path, [
			'process' => $identifiers[0],
			'id' => $identifiers[1],
		]);
		return $path;
	}
	
	public static function instance() {
		if (!isset(self::$_instance)) {
			self::$_instance = new CollectMetrics(); 
		}
		return self::$_instance;
	}

	public function start($index, $path) {
		if ($this->_timerSwitch) {
			$s = $this->_timer->start($index);
			$this->_logEntry = Hash::insert($this->_logEntry, "$path.$index.start", $s);
		}
	}
	
	public function end($index, $path) {
		if ($this->_timerSwitch) {
			$e = $this->_timer->end($index);
			$this->_logEntry = Hash::insert($this->_logEntry, "$path.$index.end", $e);
			$i = $this->_timer->interval($index);
			$this->_logEntry = Hash::insert($this->_logEntry, "$path.$index.duration", $i);
		}
	}
	
	public function logTimers($index, $path) {
		if ($this->_timerSwitch) {
			Log::write('info', serialize(Hash::extract($this->_logEntry, $path)), ['scope' => 'metrics']);
			$this->_timer->reset();
		}
	}
	
	public function __debugInfo() {
		if ($this->_timerSwitch) {
			return $this->_timer->__debugInfo();
		}
		return [];
	}
	

}

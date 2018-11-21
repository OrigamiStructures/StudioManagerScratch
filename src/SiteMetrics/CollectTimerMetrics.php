<?php
namespace App\SiteMetrics;

use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * CollectMetrics
 * 
 * This class follows the singleton pattern
 * 
 * Logs one or several timers per line. Timers are stored in serialized arrays.
 * The first two indexes establish the required structure for the array. 
 * The first index will guide selection of a class to decode the log line. 
 * The second index is a key to uniquely identitfy the data managed during 
 * the timed process. 
 * 
 * <code>
 * ArtStack => [
 *		439 => [
 *			'read' => [
 *					'start' => (float) 1542697261.8773,
 *					'end' => (float) 1542697261.8774,
 *					'duration' => (float) 0.00014996528625488
 *				],
 *			'build' => [
 *					'start' => (float) 1542697261.8775,
 *					'end' => (float) 1542697261.9196,
 *					'duration' => (float) 0.042110919952393
 *				],
 *			write' => [
 *					'start' => (float) 1542697261.9197,
 *					'end' => (float) 1542697261.9208,
 *					'duration' => (float) 0.001101016998291
 *			]
 *		]
 *	];
 * </code>
 *
 * In this case the second key is an Artwork id. This id will allow timed 
 * events on this same data to be compared. Without an identifier, other 
 * data sets that may be radically larger or smaller could be compared. 
 * Either strategy may provide useful information.
 * 
 * The keys 'read', 'build', and 'write' were provided as the $index param 
 * for ->start($index, $path) and ->end($index, $path)
 * 
 * <code>
 * $t->end('write', 'ArtStack.439');
 * </code>
 *
 * @author dondrake
 */
class CollectTimerMetrics {
	
	/**
	 * The timer object
	 *
	 * @var OSDTimer
	 */
	protected $_timer;
	
	/**
	 * The singleton instance of this class
	 *
	 * @var $this
	 */
	protected static $_instance;
	
	/**
	 * True/False setting from app.php turning timer metrics logging on/off
	 *
	 * @var boolean
	 */
	protected $_timerSwitch;
	
	/**
	 * The collector array for developing log-line data
	 *
	 * @var array
	 */
	protected $_logEntry = [];

	/**
	 * The singletons private constructor
	 */
	private function __construct() {
		$this->_timerSwitch = Configure::read('timers');
		if ($this->_timerSwitch) {
			$this->_timer = new \OSDTImer(); 
		}
	}
	
	/**
	 * The public access point for a reference to this singleton
	 * 
	 * @return CollectTimerMetrics
	 */
	public static function instance() {
		if (!isset(self::$_instance)) {
			self::$_instance = new CollectTimerMetrics(); 
		}
		return self::$_instance;
	}

	/**
	 * Create an new array structure to contain one log lines data
	 * 
	 * $path will be in the form 'strategy.id'. The strategy will be used 
	 * to select the class that will decode and analize the data in this line. 
	 * The id will be used to identify other log lines that worked on this 
	 * same data set so that timer comparisons will be apples-to-apples.
	 * 
	 * $path is returned to the calling code because timer and log calls 
	 * will require the same string so the data can be inserted in the 
	 * correct line. This system allows the accumulation of multiple sets 
	 * of log-line data at the same time; each with a different $path
	 * 
	 * @param string $path
	 * @return string The path is returned because subisquent calls require it
	 */
	public function startLogEntry($path) {
		$identifiers = explode('.', $path);
		$this->_logEntry = Hash::insert($this->_logEntry, $path, [
			'process' => $identifiers[0],
			'id' => $identifiers[1],
		]);
		return $path;
	}
	
	/**
	 * Start a new timer named $index in log-line $path
	 * 
	 * @param string $index
	 * @param string $path
	 */
	public function start($index, $path) {
		if ($this->_timerSwitch) {
			$s = $this->_timer->start($index);
			$this->_logEntry = Hash::insert($this->_logEntry, "$path.$index.start", $s);
		}
	}
	
	/**
	 * End the timer named $index, store its end and duration in log-line $path
	 * 
	 * @param string $index
	 * @param string $path
	 */
	public function end($index, $path) {
		if ($this->_timerSwitch) {
			$e = $this->_timer->end($index);
			$this->_logEntry = Hash::insert($this->_logEntry, "$path.$index.end", $e);
			$i = $this->_timer->interval($index);
			$this->_logEntry = Hash::insert($this->_logEntry, "$path.$index.duration", $i);
		}
	}
	
	/**
	 * Write log-line $path to the configured log file
	 * 
	 * The line will start with a string similar to
	 * "2018-11-20 05:21:05 Info: " followed by serialized array data. The 
	 * array data will begin with "a:" and end with "}}" followed by a newline. 
	 * 
	 * @param string $path
	 */
	public function logTimers($path) {
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

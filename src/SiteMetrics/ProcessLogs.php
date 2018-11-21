<?php
namespace App\SiteMetrics;

use Cake\Filesystem\File;
use Cake\Log\Log;
use Cake\Utility\Hash;

/**
 * ProcessLogs
 * 
 * This is all coupled directly to the metrics.log and only processes lines
 * that where process=ArtStack
 *
 * @author dondrake
 */
class ProcessLogs {
	
	protected $entries = [];
	protected $final = [];

	public function __construct() {
		$config = Log::getConfig('performanceTimers');
		$dir = $config['path'];
		$fileName = $config['file'];
		$path = "$dir$fileName.log";
		$log = new File($path);
		$log->open();
		while ($line = fgets($log->handle)) {
			if (stristr($line, '"process";s:8:"ArtStack"')) {
				$this->add($line);
			}
		}
		$log->close();
		$this->average();
		$this->aggregateAvg();
		return [$this->final, $this->entries];
	}
	
	/**
	 * sum the duration and count number of events for ArtStack access
	 * 
	 * The sum an count will allow averging. Base line start/stop/durations 
	 * are discarded and only the sums are kept
	 * 
	 * @param string $line
	 */
	private function add($line) {
		
//		preg_match('/(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2}) Info: (.*)/', $line, $match);
		preg_match('/.* Info: (.*)/', $line, $match);
		$timers = unserialize($match[1]);
		$id = $timers['id'];
		if (!isset($this->entries[$id])) {
			$this->entries[$id] = [
				'rbw' => ['count' => 0, 'duration' => 0, 'avg' => 0,],
				'build' => ['count' => 0, 'duration' => 0, 'avg' => 0,],
				'write' => ['count' => 0, 'duration' => 0, 'avg' => 0,],
				'read' => ['count' => 0, 'duration' => 0, 'avg' => 0,],
			];
		}
		if (isset($timers['build'])) {
			//record all the accumulations where cache read failed
			$this->entries[$id]['build']['count'] += 1;
			$this->entries[$id]['build']['duration'] += $timers['build']['duration'];
			$this->entries[$id]['write']['count'] += 1;
			$this->entries[$id]['write']['duration'] += $timers['write']['duration'];
			$this->entries[$id]['rbw']['count'] += 1;
			$this->entries[$id]['rbw']['duration'] += 
					($timers['build']['duration'] + 
					$timers['read']['duration'] + 
					$timers['write']['duration']);
		} else {
			//record the accumulation for successful cache reading
			$this->entries[$id]['read']['count'] += 1;
			$this->entries[$id]['read']['duration'] += $timers['read']['duration'];			
		}
	}
	
	/**
	 * Calculate the averages of all the sums
	 */
	private function average() {
		$this->final = ['performance' => 0, 'value' => 0];
		foreach ($this->entries as $id => $entry) {
			//average time to build from sql in a cached environment
			$this->entries[$id]['rbw']['avg'] = 
					$entry['rbw']['duration'] / $entry['rbw']['count'];
			//average time to build from sql excluding cache processess
			$this->entries[$id]['build']['avg'] = 
					$entry['build']['duration'] / $entry['build']['count'];
			//average time to write to the cache
			$this->entries[$id]['write']['avg'] = 
					$entry['write']['duration'] / $entry['write']['count'];
			//average time to read from cache when it contains data
			$this->entries[$id]['read']['avg'] = 
					$entry['read']['duration'] / $entry['read']['count'];
			//performance gains when reading cache vs building in a chached environemnt
			$this->final[$id]['performance'] = 
					$this->entries[$id]['rbw']['avg']/$this->entries[$id]['read']['avg'];
			
			//average time to get data in a chached environment
			$combined = ($this->entries[$id]['read']['duration'] + $this->entries[$id]['rbw']['duration'])/
					($this->entries[$id]['read']['count'] + $this->entries[$id]['rbw']['count']);
			
			//value of having a cached system, avg straight sql build vs avg cached sys access time
			$this->final[$id]['value'] = 
					$this->entries[$id]['build']['avg']/$combined;
		}
	}
	
	/**
	 * Average together all the different record averages for a total system estimate
	 */
	private function aggregateAvg() {
		$performance = 0;
		$value = 0;
		$count = count($this->final);
		foreach ($this->final as $id => $single) {
			if (!in_array($id, ['performance', 'value'])) {
				$performance += $this->final[$id]['performance'];
				$value += $this->final[$id]['value'];
			}
		}
		$this->final['performance'] = $performance/$count;
		$this->final['value'] = $value/$count;
	}
	

}

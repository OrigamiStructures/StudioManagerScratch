<?php
namespace App\Lib;


class RenumberMessaging {
	
	/**
	 *
	 * @var array Memebers are RenumberRequest objects
	 */
	private $_requests;
	
	/**
	 *
	 * @var array 
	 */
	private $_errors = [];
	
	/**
	 *
	 * @var array
	 */
	private $_summaries = [];
	
	/**
	 *
	 * @var boolean|int
	 */
	private $_error_count = FALSE;

	
	public function __construct($requests) {
		$this->_requests = $requests;
		foreach ($this->_requests as $request) {
			$message = $request->message();
			if ($request->renumber_message) {
				$this->_summaries[$request->old] = array_shift($message);
			}
			if (!empty($message)) {
				$this->_errors[$request->old] = $message;
				$this->_error_count += count($message);
			}
		}
		return $this;
	}
	
	/**
	 * 
	 * @return boolean|array
	 */
	public function errors() {
		if (!empty($this->_errors)) {
			return $this->_errors;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * 
	 * @return boolean|array
	 */
	public function summaries() {
		if (!empty($this->_summaries)) {
			return $this->_summaries;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * 
	 * @param int $number
	 * @return RenumberRequest|NULL
	 */
	public function request($number) {
		return $this->_requests[$number];
	}
	
	/**
	 * 
	 * @return boolean|int
	 */
	public function errorCount() {
		return $this->_error_count;
	}
	
}
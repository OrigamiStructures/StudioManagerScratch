<?php
namespace App\Lib;

use App\Lib\RenumberRequest;

/**
 * RenumberMessaging
 * 
 * This class receives a set of RenumberRequest objects and provides 
 * methods to summarize the requested moves and errors to the user.
 *
 * @author dondrake
 */
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

	/**
	 * @todo Is renumber_message spelled right?
	 * @param type $requests
	 * @return RenumberMessaging
	 */
	public function __construct($requests = []) {
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
	 * Return array of arrays of errors
	 * 
	 * Outer array indexed by old piece number 
	 * each containing an array of errors for that piece (empty if none) 
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
	 * Return array indexed by old piece number
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
	 * Return a specific piece request
	 * 
	 * @param int $number the old piece number
	 * @return RenumberRequest|FALSE
	 */
	public function request($number) {
		if (isset($this->_requests[$number])) {
			return $this->_requests[$number];
		}
		return FALSE;
	}
	
	/**
	 * Get the total number of errors over all requests
	 * 
	 * @return boolean|int
	 */
	public function errorCount() {
		return $this->_error_count;
	}
	
}
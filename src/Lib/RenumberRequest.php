<?php
namespace App\Lib;

/**
 * RenumberRequest manages To and From values in a renumbering request
 * 
 * One move pair is handled by each object instance
 * 
 * @todo Extend a class that knows the valid number range and make $error calculate itself
 *
 * @author dondrake
 */
class RenumberRequest {
	
	/**
	 * The original piece number
	 *
	 * @var string
	 */
	public $old;
	
	/**
	 * The new requested number
	 *
	 * @var string
	 */
	public $new;
	
	/**
	 * 
	 *
	 * @var boolean 
	 */
	public $error;
	
	/**
	 * Create and object that can provide values and messages related to renumbering pieces
	 * 
	 * @param string $old The original number
	 * @param string $new The new number requested
	 * @param boolean $error Is the new number out of range
	 */
	public function __construct($old, $new, $error = FALSE) {
		$this->old = $old;
		$this->new = $new;
		$this->error = $error;
	}
	
	public function message() {
		if ($this->error) {
			return "The numbers of pieces #$this->old and #$this->new "
						. "can't be swapped because there is no piece #$this->new.";
		} else {
			return "Change #$this->old to #$this->new.";
		}

	}
}

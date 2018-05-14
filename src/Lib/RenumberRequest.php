<?php
namespace App\Lib;

use Cake\Core\Configure;

/**
 * RenumberRequest manages To and From values in a renumbering request
 * 
 * One move pair is handled by each object instance
 *
 * @author dondrake
 */
class RenumberRequest {
	
	/**
	 * The original piece number
	 *
	 * @var string
	 */
	protected $_old;
	
	/**
	 * The new requested number
	 *
	 * @var string
	 */
	protected $_new;
	
	/**
	 * Indicates if the new number is not in the set of piece numbers
	 *
	 * @var boolean 
	 */
	public $_bad_new_number = FALSE;
	
	/**
	 * The need for this number swap was detected algoritmically
	 *
	 * @var boolean
	 */
	public $_implied_change = FALSE;
	
	/**
	 * The total number of pieces that are to receive this $new number
	 * 
	 * A truthy value here indicates this piece cannot recieve the indicated 
	 * new number because that number has been used for other pieces also. 
	 *
	 * @var int|boolean
	 */
	public $duplicate_new_number = FALSE;
	
	public $_vague_receiver = FALSE;
	
	public $_vague_provider = FALSE;
	
	public $_renumber_message = TRUE;
	
	/**
	 * Create and object that can provide values and messages related to renumbering pieces
	 * 
	 * @param string $old The original number
	 * @param string $new The new number requested
	 */
	public function __construct($old, $new) {
		$this->_old = $old;
		$this->_new = $new;
		return $this;
	}
	
	/**
	 * Give limited access to internal properties
	 * 
	 * If we are in a debugging environment, give 
	 * unlimited access to property reporting 
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {
		if (in_array($name, ['new', 'old', 'renumber_message'])) {
			return $this->{"_$name"};
			
		} elseif (Configure::read('debug')) {
			return $this->$name;
		}
		
		return NULL;
	}
		
	/**
	 * Set the property indicating a duplicate new-number error
	 * 
	 * '1' will clear the error
	 * any other value will enable duplication error messaging
	 * 
	 * @param int $count
	 */
	public function duplicate($count) {
		if (!is_null($this->_new)) {
			$this->duplicate_new_number = ($count === 1) ? FALSE : $count;
		}
		
	}
	
	/**
	 * Is the new symbol/number valid or invalid
	 * 
	 * TRUE = error, invalid new number
	 * FALSE = valid symbol
	 * 
	 * @param boolean $error_indication
	 */
	public function bad_number($error_indication) {
		$this->_bad_new_number = $error_indication;
	}
	
	public function vague_receiver($error_indication) {
		$this->_vague_receiver = $error_indication;
	}
	
	public function vague_provider($error_indication) {
		$this->_vague_receiver = $error_indication;
		return $this;
	}
	
	/**
	 * 
	 * @ Change this to allow multiple error messages (and return an array?)
	 * 
	 * @return string
	 */
	public function message() {
		$this->_message = [];
		if ($this->_bad_new_number) {
			if (is_null($this->_new)) {
				$this->_message[] = "#$this->_old was reassigned but no new number was provided.";
			} else {
				$this->_message[] = "There is no #$this->_new in this edition.";
			}
			$this->_renumber_message = FALSE;
		}
		if ($this->duplicate_new_number) {
			$this->_message[] = "Can't change multiple pieces ($this->duplicate_new_number) to #$this->_new";
			$this->_renumber_message = FALSE;
		}
		if ($this->_implied_change) {
			$this->_message[] = "Other changes implie the change of "
					. "#$this->old to #$this->new.";
			$this->_renumber_message = FALSE;
		}
		if ($this->_vague_receiver) {
			$this->_message[] = "Can't determine which piece should receive #$this->old.";
		}
		if ($this->_renumber_message) {
			array_unshift($this->_message, "Change piece #$this->_old to #$this->_new.");
		}	
		return $this->_message;
	}
	
	/**
	 * Set this object as an auto-created one
	 * 
	 * If the user says change #4 to #6, we can deduce that 
	 * #6 should become #4 even if they don't say so. If we detect 
	 * that case then the object creation would chain this 
	 * method like:
	 * $request = (new RenumberRequest($old, $new, $id))->implied();
	 * 
	 * @return \App\Lib\RenumberRequest
	 */
	public function implied($boolean) {
		$this->_implied_change = $boolean;
		return $this;
	}
}

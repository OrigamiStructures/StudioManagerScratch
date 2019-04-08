<?php
namespace App\Lib;

use Cake\Core\Configure;

/**
 * RenumberRequest manages To and From values in a renumbering request
 * 
 * One move pair is handled by each object instance. 
 * This object contains all the information about the request, the old/new 
 * numbers and any error information about the requested change.
 * 
 * This class does not perform logic to implement rules, it only records the 
 * state of the individual request. RequestNumbers does the rule logic and 
 * prods this object to set the correct state.
 * 
 * This class has one logic method, message(), which synthesizes its internal 
 * state into an array of messages that describes that state. 
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
	 * A truthy value here indicates this piece cannot receive the indicated 
	 * new number because that number has been used for other pieces also. 
	 *
	 * @var int|boolean
	 */
	public $_duplicate_new_number = FALSE;
	
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
        if (is_null($new)) {
//            $this->badNumber(TRUE);
			$this->_vague_provider = TRUE;
        }
		return $this;
	}
	
    public function newNum() {
        return $this->_new;
    }
	
    public function oldNum() {
        return $this->_old;
    }
	
	public function hasSummary() {
		return $this->_renumber_message;
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
//		if (in_array($name, ['new', 'old', 'renumber_message'])) {
//			return $this->{"_$name"};
			
//		} elseif (Configure::read('debug')) {
		if (Configure::read('debug')) {
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
			$this->_duplicate_new_number = ($count == 1) ? FALSE : $count;
		}
//        print_r("duplicate new num: $this->duplicate_new_number");
	}
	
	/**
	 * Is the new symbol/number valid or invalid
	 * 
     * NULL = no new number provided
	 * TRUE = error, invalid new number
	 * FALSE = valid symbol
	 * 
     * @param null|boolean $error_indication
	 * @param boolean $error_indication
	 */
	public function badNumber($error_indication) {
		$this->_bad_new_number = $error_indication;
	}
	
	public function vagueReceiver($error_indication) {
		$this->_vague_receiver = $error_indication;
	}
	
//	public function vague_provider($error_indication) {
//		$this->_vague_receiver = $error_indication;
//		return $this;
//	}
	
	/**
	 * Return an array of error messages for this request
	 * 
	 * Select messages based on settings in various flag properties
	 * 
	 * @todo logic for use of _renumber_message seems suspect. also is it missnamed in RenumberMessage use?
	 * 
	 * @return array Empty array if no errors
	 */
	public function message() {
        $this->_renumber_message = TRUE;
		$this->_message = [];
        
		if ($this->_vague_provider) {
			$this->_message[] = "#$this->_old was reassigned but no new number was provided.";
			$this->_renumber_message = FALSE;
		}
		if ($this->_bad_new_number) {
//			if (is_null($this->_new)) {
//				
//			} else {
				$this->_message[] = "There is no #$this->_new in this edition.";
//			}
			$this->_renumber_message = FALSE;
		}
		if ($this->_duplicate_new_number) {
			$this->_message[] = "Can't change multiple pieces ($this->_duplicate_new_number) to #$this->_new.";
			$this->_renumber_message = FALSE;
		}
		if ($this->_implied_change) {
			$this->_message[] = "Other changes implied the change of "
					. "#$this->_old to #$this->_new.";
			$this->_renumber_message = FALSE;
		}
		if ($this->_vague_receiver) {
			$this->_message[] = "Can't determine which piece should receive #$this->_old.";
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
	 * $request = (new RenumberRequest($old, $new))->implied();
	 * 
	 * @return \App\Lib\RenumberRequest
	 */
	public function implied($boolean) {
		$this->_implied_change = $boolean;
		return $this;
	}
	
	
	public function __debugInfo() {
		$properties = get_class_vars(get_class($this));
		$output = [];
		foreach ($properties as $name => $value) {
			$output[$name] = $this->$name;
		}
		return $output;
	}
	
}

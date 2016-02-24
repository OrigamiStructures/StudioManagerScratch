<?php
namespace App\View\Helper\Traits;

/**
 * Description of ValidationErrors
 *
 * @author dondrake
 */
trait ValidationErrors {
	
	/** 
	 * This error message packager may have more general use. 
	 * 
	 * CONSIDER FINDING A NEW CLASS FOR THIS
	 * 
	 * @param type $column
	 * @param type $errors
	 * @return type
	 */
	public function validationError($column, $errors) {
//		osd($errors);
		if (isset($errors[$column])) {
			return $this->Html->div('error-message', $errors[$column]);
		}
	}

	
}

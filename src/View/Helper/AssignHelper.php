<?php
namespace App\View\Helper;

use Cake\View\Helper;
use App\Lib\SystemState;

/**
 * CakePHP AssignHelper
 * @author dondrake
 */
class AssignHelper extends Helper {
	
	public $helpers = ['Html'];


	public function assignmentSources() {
		
	}
	
	public function assignmentDestinations() {
		
	}
	
	public function rangeText($provider, $edition) {
		if (in_array($edition->type, SystemState::limitedEditionTypes())) {
			$identifier = 'Numbers: ';
		} else {
			$identifier = 'Available: ';
		}
		if ($provider->hasAssignable()) {
			$text = $identifier . $provider->range($provider->assignablePieces(), $edition->type);
		} else {
			$text = 'None available';
		}
		return $this->Html->tag('span', $text, ['class' => 'range']);
		
	}
	
	public function validationError($column, $errors) {
//		osd($errors);
		if (isset($errors[$column])) {
			return $this->Html->div('error-message', $errors[$column]);
		}
	}
}

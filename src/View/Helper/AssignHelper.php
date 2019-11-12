<?php
namespace App\View\Helper;

use Cake\View\Helper;
use App\View\Helper\Traits\ValidationErrors;
use App\Lib\EditionTypeMap;

/**
 * AssignHelper supports the EditionController::assign() modelless form
 *
 * @author dondrake
 */
class AssignHelper extends Helper {

	use ValidationErrors;

	public $helpers = ['Html'];

	public function assignmentSources() {

	}

	public function assignmentDestinations() {

	}

	public function rangeText($provider, $edition) {
		if (EditionTypeMap::isNumbered($edition->type)) {
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
}

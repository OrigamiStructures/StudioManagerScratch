<?php
namespace App\View\Helper;

use Cake\View\Helper;

/**
 * FactoryHelper swaps Edition and Format helper subclasses onto a common call point
 * 
 * Edition and Format display and form output follow a variety of rules 
 * depending on the Edition type. All the view and fieldset elements are 
 * standardized and they all call a single helper variable for service. So the 
 * underlying helper class must be managed so the correct rule set is used.
 * @author dondrake
 */
class EditionFactoryHelper extends Helper {
	
	public $helpers = ['Html'];
	
	protected $_map = [
		'Unique' => 'Unique',
		'Rights' => 'Unique',
		'OpenEdition' => 'Editioned',
		'LimitedEdition' => 'Editioned',
		'Portfolio' => 'Packaged',
		'Publication' => 'Packaged',
	];
	
	public function load($type) {
		$version = str_replace(' ', '', $type);
		return $this->_View->loadHelper($this->_map[$version]);
	}
	
	public function pieceSummary($entity) {
		if (stristr(get_class($entity), 'Edition')) {
			return $this->_editionPieceSummary($entity);
		} elseif (stristr(get_class($entity), 'Format')){
			return $this->_formatPieceSummary($entity);
		} else {
			$bad_class = get_class($entity);
			throw new \BadMethodCallException(
					"Argument must be an entity of type Edition or Format. "
					. "An Entity of type $bad_class was passed.");
		}
	}

	public function pieceTools($entity) {
		if (stristr(get_class($entity), 'Edition')) {
			return $this->_editionPieceTools($entity);
		} elseif (stristr(get_class($entity), 'Format')){
			return $this->_formatPieceTools($entity);
		} else {
			$bad_class = get_class($entity);
			throw new \BadMethodCallException(
					"Argument must be an entity of type Edition. "
					. "An Entity of type $bad_class was passed.");
		}
	}

	protected function _editionPieceSummary($entity) {
		return '';
	}
	protected function _formatPieceSummary($entity) {
		return '';
	}
	protected function _editionPieceTools($entity) {
		return '';
	}
	protected function _formatPieceTools($entity) {
		return '';
	}
	
	protected function _canDispose($piece) {
		return true;
	}

}

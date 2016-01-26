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
	
	/**
	 * Map specific edition types to more general helper strategies
	 * 
	 * Unique - edition with 1 format and 1 piece
	 * Editioned - edition with n formats and n pieces
	 * Packaged - edition with 1 format and n pieces
	 *
	 * @var array
	 */
	protected $_map = [
		EDITION_UNIQUE => 'Unique',
		EDITION_RIGHTS => 'Unique',
		EDITION_LIMITED => 'Editioned',
		EDITION_OPEN => 'Editioned',
		
		PORTFOLIO_LIMITED => 'Packaged',
		PORTFOLIO_OPEN => 'Packaged',
		PUBLICATION_LIMITED => 'Packaged',
		PUBLICATION_OPEN => 'Packaged',
			];
	
	/**
	 * Factory to generate a specific helper
	 * 
	 * The edition->type is synthesized into the map key
	 * 
	 * @param type $type
	 * @return type
	 */
	public function load($type) {
//		$version = str_replace(' ', '', $type);
		return $this->_View->loadHelper($this->_map[$type]);
	}
	
	/**
	 * Get text describing the state of the pieces for this edition or format
	 * 
	 * @param Entity $entity Format or Edition
	 * @param EditionEntity $edition
	 * @return string statements describing the pieces (loose html dom nodes)
	 * @throws \BadMethodCallException
	 */
	public function pieceSummary($entity, $edition = NULL) {
		
		if (stristr(get_class($entity), 'Edition')) {
			return $this->_editionPieceSummary($entity);
			
		} elseif (stristr(get_class($entity), 'Format') &&
				stristr(get_class($edition), 'Edition')){
			return $this->_formatPieceSummary($entity, $edition);
			
		} else {
			$first_class = get_class($entity);
			$second_class = !is_null($edition) ? get_class($edition) : NULL ;
			
			throw new \BadMethodCallException(
					"Method requires an entity of type Edition or Format, or two entities of types Format and Edition. "
					. "$first_class and $second_class were passed.");
		}
	}

	/**
	 * Get tools to manage the pieces for this edition or format
	 * 
	 * @param Entity $entity Format or Edition
	 * @param EditionEntity $edition
	 * @return string tools to manage the pieces (loose html dom nodes)
	 * @throws \BadMethodCallException
	 */
	public function pieceTools($entity, $edition = NULL) {
		
		if (stristr(get_class($entity), 'Edition')) {
			return $this->_editionPieceTools($entity);
			
		} elseif (stristr(get_class($entity), 'Format') && 
				stristr(get_class($edition), 'Edition')){
			return $this->_formatPieceTools($entity, $edition);
			
		} else {
			$bad_class = get_class($entity);
			throw new \BadMethodCallException(
					"Argument must be an entity of type Edition. "
					. "An Entity of type $bad_class was passed.");
		}
	}

	protected function _editionPieceSummary($edition) {
		return '';
	}
	protected function _formatPieceSummary($format, $edition) {
		return '';
	}

	/**
	 * Generate all the Edition layer tools for piece management
	 * 
	 * Currently there is only one tool at this layer:
	 * Generate tool/link to piece assignment page for an edition
	 * 
	 * Assignment and reassignment are managed from the same page. The tool 
	 * label will say either or both words as appropriate to the circumstance
	 * 
	 * @param Entity $edition
	 */
	protected function _editionPieceTools($edition) {
		
		$assignment_tool = '';
		if ($edition->hasUnassigned()) {
			$label[] = 'Assign';
		}
		if ($edition->hasFluid()) {
			$label[] = 'Reassign';
		}
		if ($edition->hasUnassigned() || ($edition->hasFluid() && $edition->format_count > 1)) {
			$label = implode('/', $label);
			$assignment_tool = $this->Html->link("$label pieces to formats",
				['controller' => 'pieces'/*, 'action' => 'review'*/, '?' => [
					'artwork' => $edition->artwork_id,
					'edition' => $edition->id,
				]]) . "\n";
		}
		echo $assignment_tool;
	}
	
//	protected function _formatPieceTools($format, $edition) {
//		return '';
//	}
	protected function _formatPieceTools($format, $edition) {
		$PiecesTable = \Cake\ORM\TableRegistry::get('Pieces');
		$pieces = $PiecesTable->find('canDispose', ['format_id' => $format->id])->toArray();
		if ((boolean) $pieces) {
			echo $this->Html->link("Add status information",
				[/*'controller' => 'dispositions', 'action' => 'create'*/
					'controller' => 'pieces', '?' => [
					'artwork' => $edition->artwork_id,
					'edition' => $edition->id,
					'format' => $format->id,
				]]);
		} else {
			echo $this->Html->tag('p', 
				'You can\'t change the status of this artwork.', 
				['class' => 'current_disposition']
			);
		}
	}
	
	public function editionQuantitySummary($edition) {
		return '';
	}
	
}

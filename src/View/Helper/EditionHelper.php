<?php
namespace App\View\Helper;

use Cake\View\Helper;
use App\Lib\PieceFilter;
use Cake\ORM\TableRegistry;

/**
 * Base class for different Edition/Format type output
 *
 * Edition and Format display and form output follow a variety of rules
 * depending on the Edition type. All the view and fieldset elements are
 * standardized and they all call a single helper variable for service. So the
 * underlying helper class must be managed so the correct rule set is used.
 *
 * The class Lib\EditionHelperFactory provides access to the proper flavor
 * of helper based on the edition type
 *
 * @author dondrake
 */
class EditionHelper extends Helper {

	public $helpers = ['Html', 'DispositionTools'];

	protected $_pieceFilter;

	public function __construct(\Cake\View\View $View, array $config = array()) {
		parent::__construct($View, $config);
		$this->SystemState = $View->SystemState;
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

	/**
	 * Establish enviornment for a properly rendered piece table
	 *
	 * @param Entity $entity Format or Edition
	 * @param EditionEntity $edition
	 * @return string tools to manage the pieces (loose html dom nodes)
	 * @throws \BadMethodCallException
	 */
	public function pieceTable($entity, $edition = NULL) {

		if (stristr(get_class($entity), 'Edition')) {
			return $this->_editionPieceTable($entity);

		} elseif (stristr(get_class($entity), 'Format') &&
				stristr(get_class($edition), 'Edition')){
			// detection of disposition or other piece assignment
			// processes is done at the next stage
			return $this->_formatPieceTable($entity, $edition);

		} else {
			$bad_class = get_class($entity);
			throw new \BadMethodCallException(
					"Argument must be an entity of type Edition. "
					. "An Entity of type $bad_class was passed.");
		}
	}

	/**
	 * Return an instance of the piece filter/sort utility class
	 *
	 * @return PiecesUtitlity
	 */
	public function pieceFilter() {
		if (!isset($this->_pieceFilter)) {
			$this->_pieceFilter = new PieceFilter();
		}
		return $this->_pieceFilter;
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
	protected function _editionPieceTools($edition){
		return '';
	}// {

	/**
	 * Generate 'format' layer tools for 'review' pages
	 *
	 * This is currently written to allow dispo assignment of pieces, but it will
	 * need to allow portfolio or publication assignments too.
	 *
	 * @param type $format
	 * @param type $edition
	 */
	protected function _formatPieceTools($format, $edition) {
		$disposition = $this->SystemState->standing_disposition;
		if ($disposition && $this->SystemState->urlArgIsKnown('format')) {
			// in this case we can see the individual pieces with link-up tools included
			// because of redirect for flat art/edition, queryArg, not controller is our check point
			return '';
		}

		$PiecesTable = TableRegistry::getTableLocator()->get('Pieces');
		$pieces = $PiecesTable->find('canDispose', ['format_id' => $format->id])->toArray();
		$action = $disposition ? 'refine' : 'create';

		if ((((boolean) $pieces) && $format->hasSalable($edition->undisposed))
				|| $format->hasAssigned()) {
			if ($disposition) {
				$label = $this->DispositionTools->fromLabel();
			} else {
				$label = 'Transfer a piece';
			}
			echo $this->Html->link($label,
				[/*'controller' => 'dispositions', 'action' => 'create'*/
					'controller' => 'dispositions',
					'action' => $action,'?' => [
						'artwork' => $edition->artwork_id,
						'edition' => $edition->id,
						'format' => $format->id,
					]
				]);
		} else {
			echo $this->Html->tag('p',
				'You can\'t change the status of this artwork.',
				['class' => 'current_disposition']
			);
		}
	}

	public function quantityInput($edition, $edition_index) {
		return '';
	}

}

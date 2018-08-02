<?php
namespace App\View\Helper;

use Cake\View\Helper;
use CakeDC\Users\Exception\BadConfigurationException;
use BadMethodCallException;

/**
 * ArtStackElement encapsulates rendering rules to select output elements for the view
 * 
 * To insure that every time an artwork is shown on the screen, the rendering flows 
 * down through a single simple path that deposits the DOM structural nodes and loops 
 * in the appropriate places. 
 * 
 * This same set of structure-control elements is used for Create, Refine, and Review 
 * display. So there are many points in the output where rules must be evaluated to 
 * decide which final output should be used. Since there a ton of small elements 
 * involved, all the rules were moved into this helper.
 * 
 * These are very general rules that decide whether to use display or fieldset 
 * elements. Those elements, in many cases, need to make much more detailed output 
 * choices based on edition type. Those more compelx rules are found in the 
 * EditionFactoryHelper heirarchy.
 * 
 * @todo This class is being replaced by the LayersComponent. 
 * 
 * @author dondrake
 */
class ArtStackElementHelper extends Helper {
	
	protected $SystemState;

	public function __construct(\Cake\View\View $View, array $config = array()) {
		parent::__construct($View, $config);
		$this->SystemState = $this->_View->SystemState;
	}
	
	/**
	 * Return the name of the Piece element to render in this situation
	 * 
	 * Details of the values to use in the element are set by the 
	 * EditionFactory concrete helper for the edition type
	 * 
	 * @param EditionEntity|FormatEntity $entity
	 * @param EditionEntity|null $edition
	 * @return string
	 * @throws \BadMethodCallException
	 */
	public function choosePieceTable($entity, $edition = NULL) {
		if (stristr(get_class($entity), 'Edition')) {
			return $this->_editionPieceTable($entity);
			
		} elseif (stristr(get_class($entity), 'Format') &&
				stristr(get_class($edition), 'Edition')){
			return $this->_formatPieceTable($entity, $edition);
			
		} else {
			$first_class = get_class($entity);
			$second_class = !is_null($edition) ? get_class($edition) : NULL ;
			
			throw new \BadMethodCallException(
					"Method requires an entity of type Edition or Format, or two entities of types Format and Edition. "
					. "$first_class and $second_class were passed.");
		}
	}
	
	/**
	 * Based on the system state and edition, choose a piece table element
	 * 
	 * @todo This method is a functional stub. 
	 *		See _formatPieceTable() docblock for other possibly relevant details
	 *		We need to work out a rule set to flesh out 
	 *		the desired and required system function
	 * 
	 * @param entity $edition 
	 * @return string Name of the Piece Table wrapper element
	 */
	protected function _editionPieceTable($edition) {
		if (!is_null($this->SystemState->artworks)) {
			// paginated result does not render piece tables
			$element = 'empty';
		} else {
			switch ($this->SystemState->now()) {
				case ARTWORK_REVIEW:
				case ARTWORK_REFINE:
					if ($edition->hasUnassigned()) {
						$element = 'Pieces/owners_table';
					} else {
						$element = 'empty';
					}
					// default PieceHelper edition filter is ok
					break;
				default :
					$element = 'empty';
			}
		}
		return $element;
	}
	
	/**
	 * Based on the system state and format, choose a piece table element
	 * 
	 * Data specific to the edition type and current task/context is 
	 * decided on by the EditionFactoryHelper. This just sets the template.
	 * 
	 * $pieces MUST BE SET BY EditionHelper HEIRARCHY FIRST
	 * 
	 * @todo Can't this logic elimiate the need for the logic that sets 
	 *			class tag 'focus' in Elements/Format/full.ctp?
	 * 
	 * @return string Name of the element to render
	 */
	public function _formatPieceTable($format, $edition) {
		if (!is_null($this->SystemState->artworks)) {
			// paginated result does not render piece tables
			return 'empty';
		} else {
			switch ($this->SystemState->now()) {
				case ARTWORK_REVIEW:
					if (count($this->SystemState->pieces) > 0) {
						if ($this->SystemState->urlArgIsKnown('format')) {
							// single format focus allows detailed piece work
							if ($this->SystemState->standing_disposition) {
								// artist is defining a disposition. show those tools in the table
								$element = 'Pieces/dispose_pieces_table';
							} else {
								$element = 'Pieces/overview_table';
							}							
						} else {
							$element = 'Pieces/owners_table';
						}
					} else {
						$element = 'empty';
					}
					break;
				case ARTWORK_REFINE:
						$element = 'Pieces/owners_table';
					break;
				default :
					$element = 'empty';
			}
		}
		return $element;
	}
	
	/**
	 * Buttons to place after the format fieldset of the artwork stack form
	 * 
	 * While this last form fieldset may not exist for some layer refinement 
	 * they always appear during creation. And since the 'unique' tailored creation 
	 * is actualy a refinement of a empty stubbed record, we'll need a special 
	 * cancel button to delete that stub. Other special buttons may turn up.
	 */
//	public function artFinalFormButtonsRule() {
//		$buttons = $this->Form->submit('Submit', ['class' => 'button']) . "\n";
//		$buttons += $this->Form->postLink();
//	}
}

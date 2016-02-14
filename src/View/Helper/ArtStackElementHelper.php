<?php
namespace App\View\Helper;

use Cake\View\Helper;

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
 * @author dondrake
 */
class ArtStackElementHelper extends Helper {
	
	protected $SystemState;

	public function __construct(\Cake\View\View $View, array $config = array()) {
		parent::__construct($View, $config);
		$this->SystemState = $this->_View->SystemState;
	}
	
	/**
	 * Choose an element to render a layer based on context and a rule set
	 * 
	 * @param string $layer Layer that needs context rules evaluation
	 * @return string Name of the selected element
	 */
	public function choose($layer) {
		$method = "{$layer}Rule";
		return $this->$method();
	}
	
	/**
	 * The artworks div may be destined to have one or many artwork sections
	 */
	protected function artworksContentRule() {
//		osd($this->SystemState->_viewVars);
		if (!is_null($this->SystemState->artwork)) {
			$element = 'Artwork/full';			
		} elseif (!is_null($this->SystemState->artworks) && !empty ($this->SystemState->artworks)) {
			$element = 'Artwork/many';	
		} else {
			// there was no spec'd artwork and no pagination result
			// so this is a new user
			$element = 'Training/welcome_new_art';
		}
		return $element;
	}
	
	
	protected function artworkContentRule() {
		switch ($this->SystemState->now()) {
			case ARTWORK_REVIEW :
					$element = 'Artwork/describe';
				break;
			case ARTWORK_CREATE :
			case ARTWORK_REFINE :
				// Refinement much choose based on the context of the edit
				// Always display if the target is downstream
				// Always fieldset if this is the target
				if ($this->SystemState->controller() === 'artworks') {
					$element = 'Artwork/fieldset';
				} else {
					$element = 'Artwork/describe';
				}
				break;
			default :
				$element = 'Artwork/describe';
		}
//		osd($element); die;
		return $element;
	}
	
	/**
	 * Choose the Edition element to go in the current section.edition
	 * 
	 * @return string
	 */
	protected function editionContentRule() {
		$controller = $this->SystemState->controller();
		
		switch ($this->SystemState->now()) {
			case ARTWORK_REVIEW :
				$element = 'Edition/describe';
				break;
			case ARTWORK_CREATE :
				if ($controller === 'formats') {
					$element = 'Edition/describe';
				} else {
					$element = 'Edition/fieldset';
				}
				break;
			case ARTWORK_REFINE :
//				osd($this->SystemState->queryArg('edition'),'query arg');
//				osd($this->SystemState->edition->id,'edition');
						
				// Refinement much choose based on the context of the edit
				// Always display if the target is downstream
				// Always fieldset if this is the target
				// fieldset if target is upstream and this is the only child
				if ($controller === 'formats') {
					$element = 'Edition/describe';
//				} elseif ($controller === 'editions' && 
//						$this->SystemState->edition->id === $this->SystemState->queryArg('edition')) ||
//						($controller === 'artworks' && $this->SystemState->artwork->edition_count === 1)) 
				} elseif ($controller === 'editions' ||
						($controller === 'artworks' && $this->SystemState->artwork->edition_count === 1)) {
					$element = 'Edition/fieldset';
				} else {
					$element = 'Edition/describe';
				}
				break;
			default :
				$element = 'Artwork/describe';
		}
		return $element;
	}
	
	/**
	 * Choose the Format element to go in the current section.format
	 * 
	 * @return string
	 */
	protected function formatContentRule() {
		switch ($this->SystemState->now()) {
			case ARTWORK_REVIEW :
				$element = 'Format/describe';
				break;
			case ARTWORK_CREATE :
				$element = 'Format/fieldset';
				break;
			case ARTWORK_REFINE :
				$controller = $this->SystemState->controller();
				// Refinement much choose based on the context of the edit
				// Always display if the target is downstream
				// Always fieldset if this is the target
				// fieldset if target is upstream and this is the only child
				if ($controller === 'formats') {
					$element = 'Format/fieldset';
				} elseif ($controller === 'artworks' && 
						$this->SystemState->artwork->edition_count === 1 &&
						$this->SystemState->edition->format_count === 1) {
					$element = 'Format/fieldset';
				} elseif ($controller === 'editions' && 
						$this->SystemState->edition->format_count === 1) {
					$element = 'Format/fieldset';
				} else {
					$element = 'Format/describe';
				}
				break;
			default :
				$element = 'Artwork/describe';
		}
		return $element;
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
	protected function _editionPieceTable($edition) {
		if (!is_null($this->SystemState->artworks)) {
			// paginated result does not render piece tables
			return 'empty';
		} else {
			switch ($this->SystemState->now()) {
				case ARTWORK_REVIEW:
				case ARTWORK_REFINE:
					if ($edition->format_count === 1 && !$edition->hasUnassinged()) {
						$element = 'empty';
					} else {
						$element = 'Pieces/owners_table';
					}
					// default PieceHelper edition filter is ok
					return $element;
					break;
				default :
					return 'empty';
			}
		}
	}
	
	/**
	 * Choose wich pieces element has the info needed in this context
	 * 
	 * Data specific to the edition type and current task/context is 
	 * decided on by the EditionFactoryHelper. This just sets the template.
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
				case ARTWORK_REFINE:
					// default PieceHelper format filter is ok
					return 'Pieces/owners_table';
					break;
				default :
					return 'empty';
			}
		}
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

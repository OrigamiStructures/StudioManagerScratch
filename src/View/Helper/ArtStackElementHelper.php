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
		} elseif (!is_null($this->SystemState->artworks)) {
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
//			default :
//				$element = 'Artwork/describe';
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
		}
		return $element;
	}
	
}

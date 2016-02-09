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
class ArtStackElement extends Helper {
	
	protected $SystemState;

	public function __construct(\Cake\View\View $View, array $config = array()) {
		parent::__construct($View, $config);
		$this->SystemState = $this->_View->SystemState;
	}
	
	public function choose($layer, $context) {
		$method = "{$layer}Rule";
		return $this->$method($context);
	}
	
	/**
	 * The artworks div may be destined to have one or many artwork sections
	 */
	protected function artworksContentRule() {
//		osd($this->SystemState->_viewVars);
		if (is_null($this->SystemState->artworks)) {
			$element = 'Artwork/full';
		} else {
			$element = 'Artwork/many';		
		}
		return $element;
	}
	
	protected function artworkRule($context) {
		switch ($this->SystemState->now()) {
			case ARTWORK_REVIEW :
				
				$element = 'Artwork/full';
				break;
			case ARTWORK_CREATE :
			case ARTWORK_REFINE :
				$element = 'Artwork/filedset';
				break;
		}
		return $element;
	}
	
}

<?php

namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * CakePHP LayerElementComponent
 * 
 * All controller actions served by this component render Artworks through 
 * a mix-and-match collection of elements. The elements are grouped into 
 * four layers: Decoration, Artwork, Edition, and Format. The Decoration 
 * layer allows different forms to wrap the page to provide functionality 
 * beyond simple display.  
 * 
 * The general goal to keep css simple and consistent by rigidly maintaining 
 * the DOM pattern. To this end, there are elements that loop through the 
 * provided entities at each layer and, based on instructions provided by 
 * this component, select an element to render the entity content.
 * 
 * This technique should allow a small number of elements to be mixed to 
 * create a wide variety of purpose built views. At the same time, this 
 * component provides a central location to consider and compare all the 
 * possible view outcomes and to understand the logic of element selection. 
 * 
 * Simple arrays are returned rather than associative maps for simplicity. 
 * To keep use in the templates and elements easy to understand, a set of 
 * constants has been created in bootstrap.php to stand in for the indexes:
 * define('WRAPPER_LAYER', 0);
 * define('ARTWORK_LAYER', 1);
 * define('EDITION_LAYER', 2);
 * define('FORMAT_LAYER', 3);
 * 
 * The results are passed to the View in $elements. Fetching the identity 
 * of the required layer will look like this:
 * 
 * <pre>
 * // for a simple string return
 * echo $this->element($element[ARTWORK_LAYER]);
 * // for a closure requiring an entity argument
 * // and assuming $artwork is the current entity
 * $edition_element = $element[EDITION_LAYER]($artwork);
 * 
 * @author dondrake
 * @todo Retreving layer element names will need to be standardized. It's 
 *		possible that even in some simple cases where a string could be 
 *		returned, a closure will have to be designed to make them standard. 
 *		Or some helper might be designed to handle the different array 
 *		element content that is encountered. Standardization feels better.
 */
class LayersComponent extends Component {
	
	/**
	 * The single call point for all served actions 
	 * 
	 * Based on the current controller/action, will return 
	 * an array with the name or a closure that returns the name 
	 * of the element to use at each layer.
	 * 
	 * @return array
	 */
	public function setElements() {
		$method =  lcfirst($this->request->controller) . ucfirst($this->request->action);
		return $this->$method();
	}

	protected function artworksReview() {
		return [
			'Artwork/no_decoration',
			'Artwork/describe',
			'Edition/describe',
			'Format/describe',
		];
	}
	
	protected function artworksRefine() {
		return [
			'Artwork/form_decoration',
			'Artwork/fieldset',
			function() {
				return $this->SystemState->artwork->edition_count === 1 ? 
					'Edition/fieldset' : 'Edition/describe';
			},
			function() {
				return $this->SystemState->artwork->edition_count === 1 &&
				$this->SystemState->edition->format_count === 1 ?
				'Format/fieldset' : 'Format/describe';
			}
		];
	}
	
	protected function artworksCreate() {
		return [
			'Artwork/form_decoration',
			'Artwork/fieldset',
			'Edition/fieldset',
			'Format/fieldset',
			];
	}
	
	protected function artworksCreateUnique() {
		return [
			'Artwork/createunique_decoration',
			'Artwork/create_unique',
			'Edition/create_unique',
			'Format/create_unique',
		];
	}
	
	protected function editionsReview() {
		return [
			'Artwork/no_decoration',
			'Artwork/describe',
			'Edition/describe',
			'Format/describe',
		];
	}
	
	protected function editionsRefine() {
		return [
			'Artwork/form_decoration',
			'Artwork/describe',
			'Edition/fieldset',
			$artwork->edition->format_count === 1 ? 
				'Format/fieldset' : 'Format/describe',
		];
	}
	
	protected function editionsCreate() {
		return [
			'Artwork/form_decoration',
			'Artwork/describe',
			'Edition/fieldset',
			'Format/fieldset',
		];
	}
	
	protected function editionsAssign() {
		return [
			'Artwork/form_decoration',
			'Artwork/describe',
			'Edition/describe',
			'Format/describe',
		];
	}
	
	protected function formatsReview() {
		return [
			'Artwork/no_decoration',
			'Artwork/describe',
			'Edition/describe',
			'Format/describe',
		];
	}
	
	protected function formatsRefine() {
		return [
			'Artwork/form_decoration',
			'Artwork/describe',
			'Edition/describe',
			'Format/fieldset',
		];
	}
	
	protected function formatsCreate() {
		return [
			'Artwork/form_decoration',
			'Artwork/describe',
			'Edition/describe',
			'Format/fieldset',
		];
	}
	
	protected function piecesRenumber() {
		return [
			'Artwork/form_decoration',
			'Artwork/describe',
			'Edition/describe',
			'Format/describe',
		];
	}
}

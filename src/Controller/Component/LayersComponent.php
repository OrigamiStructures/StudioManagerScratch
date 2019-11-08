<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Collection\Collection;
use App\Model\Entity\Artwork;
use App\Model\Entity\Edition;
use App\Model\Entity\Format;
use Cake\Utility\Hash;

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
 */
class LayersComponent extends Component {

    public function initialize(array $config)
	{
		$this->controller = $this->getController();
		$this->SystemState = $this->controller->SystemState;
	}

	/**
	 * The single call point for all served actions
	 *
	 * Based on the current controller/action, will return
	 * an array of closures that return the name of the
	 * element to use at each layer.
	 *
	 * @return array
	 */
	public function setElements() {
		$method =  lcfirst($this->getController()->getRequest()->getParam('controller'))
            . ucfirst($this->getController()->getRequest()->getParam('action'));
		$simple_result = new Collection($this->$method());
		/*
		 * Insure every element is a callable to standardize access
		 */
		$result = $simple_result->map(function($value){
			return is_string($value) ?
				function() use ($value) {return $value;} :
				$value;
			}
		);
		return $result->toArray();
	}

	protected function artworksReview() {
		return [
			'Artwork/no_decoration',
			function (Artwork $artwork) {
				return $this->hasFocus($artwork) ?
					'Artwork/describe' : 'Artwork/summary';
			},
			'Edition/summary',
			'Format/summary',
		];
	}

    /**
     * @todo This method doesn't seem to be used. But I added the parameter
     *      to remove SystemState references.
     * @param $artwork Artwork
     * @param $edition Edition
     * @return array
     */
	protected function artworksRefine($artwork, $edition) {
		return [
			'Artwork/form_decoration',
			'Artwork/fieldset',
			function() {
				return $artwork->edition_count === 1 ?
					'Edition/fieldset' : 'Edition/describe';
			},
			function() {
				return $artwork->edition_count === 1 &&
				$edition->format_count === 1 ?
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
			function (Edition $edition) {
				return $this->hasFocus($edition) ?
					'Edition/describe' : 'Edition/summary';
			},
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
			'Edition/piece_assignment_decoration',
			'Artwork/describe',
			'Edition/describe',
			'Format/describe',
		];
	}

	/**
	 * @todo Should the parent elements watch for focus too? How much detail?
	 * @return array
	 */
	protected function formatsReview() {
		return [
			'Artwork/no_decoration',
			'Artwork/summary',
			'Edition/summary',
			function (Format $format) {
				return $this->hasFocus($format) ?
					'Format/describe' : 'Format/summary';
			},
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

    /**
     * Is an ID'd record referenced in the URL arguments?
     *
     * hasFocus('artwork', 641)
     * hasFocus('member', 1215)
     * or
     * hasFocus($artwork) // Artwork entity
     * hasFocus($format) // Format entity
     *
     * @param entity|string $name
     * @param string $value
     * @return boolean
     */
    public function hasFocus($name, $value = NULL) {
        $queryArgs = $this->getController()->request->getQueryParams();

        if (is_object($name)) {
            $value = $name->id;
            $name = $this::stripNamespace($name);
        }
        $argValue = Hash::get($queryArgs, $name);
        if (!is_null($argValue)) {
            $result = $argValue == $value;
        } else {
            $result = false;
        }
        return $result;
    }
}

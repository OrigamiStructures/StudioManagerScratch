<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\HtmlHelper;
use BadMethodCallException;
use Cake\Utility\Inflector;

/**
 * CakePHP ToolLinkHelper
 * @author dondrake
 */
class ToolLinkHelper extends Helper {

	/**
	 * This list of tools that can be linked
	 *
	 * @var array
	 */
	protected $_tools = ['review', 'refine', 'remove', ];

	/**
	 * The query arguments for the link() method
	 *
	 * This get built fresh during the Layer validation process.
	 *
	 * @var array
	 */
	protected $_query = [];

	protected $_urlConfig = [];

	public $helpers = ['Html'];

	/**
	 *
	 * @todo add delimeters option?
	 *
	 * @param string $layer
	 * @param array $toolSet
	 */
	public function links($layer, array $toolSet) {
		$this->_urlConfig = [];
		$this->_validateTools($toolSet);
		$this->_validateLayer($layer); //sets $_query array
		$this->_urlConfig = [
			'controller' => Inflector::pluralize($layer),
			'?' => $this->_query,
		];
		$toolLinks = '';
		foreach ($toolSet as $toolName) {
			$toolLinks .= $this->{$toolName . 'Link'}($this->_urlConfig) . ' ';
		}
        return $this->Html->tag('span', $toolLinks, ['class' => 'inline_nav']);
		return $toolLinks;
	}

	/**
	 * Insure the requested layer is valid and build the $_query array
	 *
	 * The layer must be one of the known layers and all the nodes
	 * leading to it (its parent chain) must have been placed in the
	 * expected variables.
	 *
	 * @param string $layer
	 * @throws BadMethodCallException
	 */
	private function _validateLayer($layer) {
		$this->_query = [];
		if (!in_array($layer, $this->_layers)) {
			throw new BadMethodCallException("'$layer' is not a valid focus node for an $this->alias tool.");
		}
		$matched = FALSE;
		$index = 0;
		while (!$matched && $index < count($this->_layers)) {
			$variableName = $this->_layers[$index];
			$entity = $this->getView()->get($variableName);
			if (is_a($entity, ucfirst('App\\Model\\Entity\\'.$variableName))) {
				$this->_query[$variableName] = $entity->id;
				$index++;
				$matched = $layer === $variableName;
			} else {
				$this->_query = [];
				throw new BadMethodCallException("The variable '$variableName' has not been set.");
			}
		}
	}

	/**
	 * Insure the requested tools are supported
	 *
	 * @param array $toolSet
	 * @throws BadMethodCallException
	 */
	private function _validateTools($toolSet) {
		foreach ($toolSet as $toolName) {
			if (!in_array($toolName, $this->_tools)) {
				throw new BadMethodCallException("'$toolName' is not an available $this->alias tool.");
			}
		}
	}

    /**
     * Return the review link based upon provided url array
     *
     * @param array $url
     * @return string
     */
    public function reviewLink($url) {
        return $this->Html->link($this->icon(ICON_COG, 'medium'),
				$url + ['action' => 'review'], ['escape' => FALSE]);
    }

    /**
     * Return the refine link based upon provided url array
     *
     * @param array $url
     * @return string
     */
    public function refineLink($url) {
        return $this->Html->link($this->icon(ICON_REFINE, 'medium'),
				$url + ['action' => 'refine'], ['escape' => FALSE]);
    }

    /**
     * Return the delete link based upon provided url array
     *
     * @param array $url
     * @return string
     */
    public function removeLink($url) {
        return $this->Html->link($this->icon(ICON_REMOVE, 'medium'),
				$url + ['action' => 'remove'], ['escape' => FALSE]);
    }

    /**
     * Return Foundation icons with size
	 *
	 * @todo This isn't specific to this helper. A trait?
     *
     * @param string $icon
     * @param string $size, 'small', 'medium', or 'large'
     * @return string
     */
    public function icon($icon, $size = 'small') {
        if(!in_array($size, ['small', 'medium', 'large'])){
            $size = 'small';
        }
        return $this->Html->tag('i','',['class' => "$icon $size"]);
    }

}

<?php
namespace App\View\Helper;

use Cake\View\Helper\HtmlHelper;

/**
 * CakePHP NestedListHelper
 * @author dondrake
 */
class DropDownHelper extends HtmlHelper {
	
	private $level = 0;
	
	private $ul_template = [
		'<ul class="dropdown menu" data-dropdown-menu>{{content}}</ul>',
		'<ul{{attrs}}>{{content}}</ul>',
//		'<ul class="submenu menu vertical" data-submenu>{{content}}</ul>'
		];



	/**
     * Build a nested list (UL/OL) out of an associative array.
     *
     * Options for $options:
     *
     * - `tag` - Type of list tag to use (ol/ul)
     *
     * Options for $itemOptions:
     *
     * - `even` - Class to use for even rows.
     * - `odd` - Class to use for odd rows.
     *
     * @param array $list Set of elements to list
     * @param array $options Options and additional HTML attributes of the list (ol/ul) tag.
     * @param array $itemOptions Options and additional HTML attributes of the list item (LI) tag.
     * @return string The nested list
     * @link http://book.cakephp.org/3.0/en/views/helpers/html.html#creating-nested-lists
     */
    public function nestedList(array $list, array $options = [], array $itemOptions = [])
    {
		if($this->level < 2) {
			$this->templates(['ul' => $this->ul_template[$this->level]]);
		}
        $options += ['tag' => 'ul'];
        $items = $this->_nestedListItem($list, $options, $itemOptions);
        return $this->formatTemplate($options['tag'], [
            'attrs' => $this->templater()->formatAttributes($options, ['tag']),
            'content' => $items
        ]);
    }

    /**
     * Internal function to build a nested list (UL/OL) out of an associative array.
     *
     * @param array $items Set of elements to list.
     * @param array $options Additional HTML attributes of the list (ol/ul) tag.
     * @param array $itemOptions Options and additional HTML attributes of the list item (LI) tag.
     * @return string The nested list element
     * @see HtmlHelper::nestedList()
     */
    protected function _nestedListItem($items, $options, $itemOptions)
    {
        $out = '';

        $index = 1;
        foreach ($items as $key => $item) {
            if (is_array($item)) {
                $item = $key . $this->nestedList($item, $options, $itemOptions);
            }
            if (isset($itemOptions['even']) && $index % 2 === 0) {
                $itemOptions['class'] = $itemOptions['even'];
            } elseif (isset($itemOptions['odd']) && $index % 2 !== 0) {
                $itemOptions['class'] = $itemOptions['odd'];
            }
            $out .= $this->formatTemplate('li', [
                'attrs' => $this->templater()->formatAttributes($itemOptions, ['even', 'odd']),
                'content' => "<a href=\"#\">$item</a>"
            ]);
            $index++;
        }
        return $out;
    }

}

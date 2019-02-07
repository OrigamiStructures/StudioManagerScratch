<?php
namespace App\View\Helper;

use Cake\View\Helper\HtmlHelper;
use Cake\View\View;
use Cake\Collection\Collection;

/**
 * DropDownHelper overrides HtmlHelper's nestedList() to make Foundation DropDowns
 * 
 * 
 * 
 * CakePHP DropDownHelper
 * @author dondrake
 */
class DropDownHelper extends HtmlHelper {
	
	private $level = 0;
	
	private $dropdown_template = [
//		'ul-dropdown' => [
//			'template' => "<ul{{attrs}} data-dropdown-menu>\n{{content}}</ul>\n",
//			'attrs' => ['class' => ['dropdown', 'menu']]],
//		'ul-sub-dropdown' => [
//			'template' => "<ul{{attrs}} data-submenu>\n{{content}}</ul>\n",
//			'attrs' => ['class' => ['nested', 'submenu', 'menu', 'vertical', 'hidden']]],
//		'li-has-submenu' => [
//			'template' => "\t<li{{attrs}}>\n\t\t{{content}}\n\t</li>\n",
//			'attrs' => ['class' => ['has-submenu']]],
		'ul-dropdown' => [
			'template' => "<ul{{attrs}} >\n{{content}}</ul>\n",
			'attrs' => ['class' => ['menu']]],
		'ul-sub-dropdown' => [
			'template' => "<ul{{attrs}}>\n{{content}}</ul>\n",
			'attrs' => ['class' => ['nested', 'menu', 'vertical']]],
		'li-has-submenu' => [
			'template' => "\t<li{{attrs}}>\n\t\t{{content}}\n\t</li>\n",
			'attrs' => ['class' => ['has-submenu']]],
		];
	
		protected $attributes;

		public function __construct(\Cake\View\View $View, array $config = array()) {
			parent::__construct($View, $config);
			$t_coll = new Collection($this->dropdown_template);
			$templates = [];
			$attributes = [];
			$t_coll->each(function($value, $key) use (&$templates, &$attributes) {
				$templates[$key] = $value['template'];
				$this->attributes[$key] = $value['attrs'];
			});
			$this->templater()->add($templates);
//			osd($this->templater()->_compiled);
		}

    public function menu(array $list, array $options = [], array $itemOptions = [])
	{
		return $this->nestedList($list, $options, $itemOptions);
	}
	
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
		if ($this->level === 0) {
			$options += ['tag' => 'ul-dropdown'];
			$ul_options = $this->merge($options, $this->attributes['ul-dropdown']);
		} else {
			$options += ['tag' => 'ul-sub-dropdown'];
			$ul_options = $this->merge($options, $this->attributes['ul-sub-dropdown']);
		}
//        $options += ['tag' => 'ul'];
        $items = $this->_nestedListItem($list, $options, $itemOptions);
        return $this->formatTemplate($ul_options['tag'], [
            'attrs' => $this->templater()->formatAttributes($ul_options, ['tag']),
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
				$this->level++;
                $item = $key . $this->nestedList($item, $options, $itemOptions);
				$this->level--;
            }
            if (isset($itemOptions['even']) && $index % 2 === 0) {
                $itemOptions['class'] = $itemOptions['even'];
            } elseif (isset($itemOptions['odd']) && $index % 2 !== 0) {
                $itemOptions['class'] = $itemOptions['odd'];
            }
			$nodes = preg_split('/</', $item);
			$link = array_shift($nodes);
			$li_children = !empty($nodes) ? '<' . implode('<', $nodes) : '';
//			osd([$key, $nodes, $link, $item], 'key, nodes, link and item');
			$li_content = $this->liContent($key, $link);
			
			// don't include empty sub menus
			if (stristr($li_children, "\" >\n</ul>")) {
				$li_children = '';
			}
			
			if (!empty($li_children)) {
//				osd('has sub chosen');
				$template = 'li-has-submenu';
				$attributes = ['class' => []];
				if (!stristr($li_content, '<a ')) {
					$attributes = $this->attributes['li-has-submenu'];
					$attributes['class'][] = 'menu-text';
				}
				$li_options = $this->merge($itemOptions, $attributes);
			} else {
//				osd('li chosen');
				$template = 'li';
				$attributes = ['class' => []];
				if (!stristr($li_content, '<a ')) {
					$attributes = ['class' => ['menu-text', empty($li_children) ? ' disabled' : '']];
				}
				$li_options = $this->merge($itemOptions, $attributes);
			}
            $out .= $this->formatTemplate($template, [
                'attrs' => $this->templater()->formatAttributes($li_options, ['even', 'odd']),
                'content' => $li_content . $li_children
            ]);
            $index++;
        }
        return $out;
    }
	
	protected function liContent($key, $link) {
		if ($key == $link) {
			return $key; // "<a href=\"#\">$key</a>"; //
		} else {
			return "<a href=\"$link\">$key</a>";
		}
	}

		protected function merge($options, $defaults){
//		osd(func_get_args());
		if (!isset($options['class'])) {
			$options['class'] = implode(' ', $defaults['class']);
		} else {
			if (is_array($options['class'])) {
				$class = $options['class'];
			} else {
				$class = array_flip(explode(' ', $options['class']));
			}
			$required = array_flip($defaults['class']);
			// can array_unique() be used here?
			$result = array_flip($class + $required);
			$options['class'] = implode(' ', $result);
		}
		return $options;
	}

}

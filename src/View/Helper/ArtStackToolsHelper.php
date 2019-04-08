<?php
namespace App\View\Helper;

use App\View\Helper\ToolLinkHelper;

/**
 * ArtworkReviewHelper returns focused action links for some artwork layer
 * 
 * Artworks are organized into Artwork/Edition/Format/Piece layers. The user 
 * may want to take action on any specific node of this stack. During the 
 * nested loop process of rendering, the current Entity for a node gets 
 * moved to a standard variable; $artwork, $edition, $format, piece. 
 * So, given an appropriate layer pointer, the helper can discover IDs for 
 * the entities and construct links that can target specific nodes and 
 * the paths to those nodes.
 * 
 * These facts allow this Helper to encapsulate all the processes 
 * to generate a variety of tool links. The UX may at some point require 
 * 'flavors' of links for different situations, but for now, all the tool 
 * sets are returned as inline icon sets.
 * 
 * @author jasont
 * @author dondrake
 */
class ArtStackToolsHelper extends ToolLinkHelper {
	
	protected $alias = 'ArtStackTools';
	
	/**
	 * The list of layers that can get targeted tools
	 *
	 * @var array
	 */
	protected $_layers = ['artwork', 'edition', 'format', 'piece'];
    
    /**
     * Returns an inline review/refine control set
	 * 
	 * receives something like this:
	 * [ 'controller' => 'artworks, ? => [ 'artwork' => 3 ] ]
	 * and the tools will add the proper action node to the array
     * 
     * @param array $url
     * @param array $refine_url, an optional second array for a different url for refines
     * @return string
     */
    public function inlineReviewRefine($url, $refine_url = NULL) {
        $refine_url = (is_null($refine_url)) ? $url : $refine_url;
        $review = $this->reviewLink($url);
        $refine = $this->refineLink($refine_url);
        return $this->Html->tag('span', "$review $refine", ['class' => 'inline_icon nav']);
    }
    
    /**
     * Returns an inline review/delete control set
	 * 
	 * receives something like this:
	 * [ 'controller' => 'artworks, ? => [ 'artwork' => 3 ] ]
	 * and the tools will add the proper action node to the array
     * 
     * @param array $url
     * @param array $refine_url, an optional second array for a different url for refines
     * @return string
     */
    public function inlineReviewDelete($url, $remove_url = NULL) {
        $remove_url = (is_null($remove_url)) ? $url : $remove_url;
        $review = $this->reviewLink($url);
        $remove = $this->removeLink($remove_url);
        return $this->Html->tag('span', "$review $remove", ['class' => 'inline_nav']);
    }
	
	/**
	 * Returns an inline review/refine/delete control set
	 * 
	 * receives something like this:
	 * [ 'controller' => 'artworks, ? => [ 'artwork' => 3 ] ]
	 * and the tools will add the proper action node to the array
	 * 
	 * @param type $url
	 * @param type $refine_url
	 * @param type $remove_url
	 * @return type
	 */
	public function inlineReviewRefineDelete($url, $refine_url = NULL, $remove_url = NULL) {
        $refine_url = (is_null($refine_url)) ? $url : $refine_url;
        $remove_url = (is_null($remove_url)) ? $url : $remove_url;
        $review = $this->reviewLink($url);
        $refine = $this->refineLink($refine_url);
        $remove = $this->removeLink($remove_url);		
        return $this->Html->tag('span', "$review $refine $remove", ['class' => 'inline_nav']);
	}
    
}

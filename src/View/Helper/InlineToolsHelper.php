<?php

/*
 * Copyright 2015 Origami Structures
 */

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\HtmlHelper;

/**
 * CakePHP ArtworkReviewHelper
 * @author jasont
 */
class InlineToolsHelper extends Helper {
    
    public $helpers = ['Html'];
    
    /**
     * Return the review link based upon provided url array
     * 
     * @param array $url
     * @return string
     */
    public function reviewLink($url) {
        return $this->Html->link($this->icon(ICON_REVIEW, 'medium'), $url + ['action' => 'review'], ['escape' => FALSE]);
    }
    
    /**
     * Return the refine link based upon provided url array
     * 
     * @param array $url
     * @return string
     */
    public function refineLink($url) {
        return $this->Html->link($this->icon(ICON_REFINE, 'medium'), $url + ['action' => 'refine'], ['escape' => FALSE]);
    }
    
    /**
     * Return the delete link based upon provided url array
     * 
     * @param array $url
     * @return string
     */
    public function removeLink($url) {
        return $this->Html->link($this->icon(ICON_REMOVE, 'medium'), $url + ['action' => 'remove'], ['escape' => FALSE]);
    }
    
    /**
     * Return Foundation icons with size
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
    
    /**
     * Returns an inline review/refine control set
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
	
	public function inlineReviewRefineDelete($url, $refine_url = NULL, $remove_url = NULL) {
        $refine_url = (is_null($refine_url)) ? $url : $refine_url;
        $remove_url = (is_null($remove_url)) ? $url : $remove_url;
        $review = $this->reviewLink($url);
        $refine = $this->refineLink($refine_url);
        $remove = $this->removeLink($remove_url);		
        return $this->Html->tag('span', "$review $refine $remove", ['class' => 'inline_nav']);
	}
    
}

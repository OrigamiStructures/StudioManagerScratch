<?php
namespace App\View\Helper;

use Cake\View\Helper;

/**
 * CakePHP PieceTableHelper
 * 
 * @todo This is a stub idea and might switch over to be tools on the EditionEntity
 * or and ArtStack or EditionStack object.
 * 
 * @author dondrake
 */
class PieceTableHelper extends Helper {
	
	/**
	 * Build a lookup to get assigned owner titles for pieces
	 * 
	 * The Edition, Format and Piece entities all have a key() method that 
	 * generates values that can identify the assigned owner of (edition or 
	 * format) for a piece.
	 * 
	 * This method builds a hash map so that $piece->key() will give the  
	 * index of the owners title. 
	 * 
	 * $edition->key() = eg: 122_, 3175_
	 * $format->key() = eg: 122_42, 3175_1227
	 * $piece->key() = eg: 122_, 3175_, 122_42, 3175_1227
	 * 
	 * @param array $providers Elements are an Edition and its Format (all entities)
	 * @return array The hash map
	 */
	public function buildOwnerTitleLookup($providers) {
		$owners = new \Cake\Collection\Collection($providers);
		return $owners->reduce(function($accumulator, $owner) {
			$accumulator[$owner->key()] = $owner->display_title;
			return $accumulator;
		}, []);
	}
	
	public function owner_title($piece, $providers) {
		
	}
}

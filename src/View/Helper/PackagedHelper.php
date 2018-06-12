<?php
namespace App\View\Helper;

use Cake\View\Helper;
use App\View\Helper\EditionHelper;

/**
 * PackagedHelper: rule based view/tool rendering for Portfolio and Publication
 * 
 * Portfolios and Publications follow the general patterns of Open/Limited but 
 * I want some more specific languag on the disposition tool. They are the two 
 * Edition types that package other Edition pieces into collections. Portfolio 
 * gathers artwork pieces and Publication gathers rights for artworks. 
 * 
 * @author dondrake
 */
class PackagedHelper extends EditionedHelper {
	
/**
 * This may have special tools that link back to the contained Artworks
 * 
 */	
	protected function _formatPieceSummary($format, $edition) {
		$type = strtolower($edition->type);
		$plural_type = \Cake\Utility\Inflector::pluralize($type);
		//		"There are up to z pieces for sale";
		$fluid = $edition->fluid_piece_count;
		if ($format->hasSalable($fluid)) {
			$grammar = $format->salable_piece_count($fluid) === 1 ?
					"There might be 1 $type" :
					"There are up to {$format->salable_piece_count($fluid)} $plural_type";
			$salable = sprintf("<p>%s available for sale.</p>\n", $grammar);
		} else {
			$salable = "<p class=\"sold_out\">SOLD OUT</p>\n";
		}
		echo $salable;
	}
	
	
}

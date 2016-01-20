<?php
namespace App\View\Helper;

use Cake\View\Helper;
use App\View\Helper\EditionFactoryHelper;

/**
 * UniqueHelper: rule based view/tool rendering for Unique and Rights Editions
 * 
 * Unique and Rights editions have one Piece and one Format. 
 *   
 * @author dondrake
 */
class UniqueHelper extends EditionFactoryHelper {
	
	public function _editionPieceSummary($edition) {
		return '';
	}
	
	public function _editionPieceTools($edition) {
		return '';
	}
	
	
}

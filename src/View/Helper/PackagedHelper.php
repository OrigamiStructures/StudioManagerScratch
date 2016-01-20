<?php
namespace App\View\Helper;

use Cake\View\Helper;
use App\View\Helper\EditionFactoryHelper;

/**
 * PackagedHelper: rule based view/tool rendering for Portfolio and Publication
 * 
 * Portfolios and Publications are similar in that they can have multiple 
 * Pieces both only one Format per Edition. They are the two Edition types 
 * that package other Edition pieces into collections. Portfolio gathers 
 * other tangible artwork pieces and Publication gathers Rights for artworks. 
 * 
 * @author dondrake
 */
class PackagedHelper extends EditionFactoryHelper {
	
	public function pieceSummary($entity) {
		if (stristr(get_class($entity), 'Edition')) {
			$this->_editionPieceSummary($entity);
		} elseif (stristr(get_class($entity), 'Format')){
			$this->_formatPieceSummary($entity);
		}
	}
	
	public function pieceTools($entity) {
		return '';
	}
	
	
}

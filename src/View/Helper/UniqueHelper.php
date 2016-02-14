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
	
	protected function _formatPieceSummary($format, $edition) {
		$piece = $format->pieces[0];
		if((boolean) $piece->disposition_count) {
			return '<p>pending results</p>';
		} else {
			echo $this->Html->tag('p', 
				'You haven\'t recorded the status of this work', 
				['class' => 'current_disposition']
			);
		}
	}
	
	/**
	 * Override tools for this one-piece-only special case
	 * 
	 * We can move the artist further down the editing path because 
	 * in this case we know there is only one piece. 
	 * 
	 * @param FormatEntity $format
	 * @param EditionEntity $edition
	 */
	protected function _editionPieceTools($edition) {
		return '';
	}

	protected function _editionPieceSummary($edition) {
		return '';
	}

	public function quantityInput($edition, $edition_index) {
		return '';
	}

	protected function _editionPieceTable($edition) {
		return '';
	}

	protected function _formatPieceTable($format, $edition) {
		$caption = 'Details about this work';
		$pieces = $format->pieces;
		$providers = [$format];
		
		$this->_View->set(compact('caption', 'pieces', 'providers'));
	}

}
	
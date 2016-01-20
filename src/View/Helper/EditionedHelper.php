<?php
namespace App\View\Helper;

use App\View\Helper\EditionFactoryHelper;

/**
 * EditionedHelper: rule based view/tool rendering for Limited and Open Editions
 * 
 * Limited and Open editions both support multiple formats and multiple pieces. 
 * These two Editions types require the most display reporting to express thier 
 *   
 * @author dondrake
 */
class EditionedHelper extends EditionFactoryHelper {

	protected function _editionPieceSummary($edition) {
					/**
					 * =========================================================
					 * This should be a 'reassign' tool subject to Edition rules
					 */
					$piece_count = $edition->quantity - $edition->assigned_piece_count;
					$piece_label = $piece_count === 1 ? 'piece' : 'pieces';
					if ($piece_count !== 0) {
//						echo $this->Html->tag('p', "There are no unassigned pieces for this edition");
//					} else {
						echo $this->Html->link("Details about the $piece_count unassigned $piece_label in this edition",
								['controller' => 'pieces', 'action' => 'review', '?' => [
									'artwork' => $edition->artwork_id,
									'edition' => $edition->id,
								]]);
					}
					// report on unmade pieces
	}
	
	protected function _editionPieceTools($edition) {
		
	}
		
	protected function _formatPieceTools($edition) {
		
	}
	
}

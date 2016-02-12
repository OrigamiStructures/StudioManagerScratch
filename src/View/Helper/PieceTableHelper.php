<?php
namespace App\View\Helper;

use Cake\View\Helper;

/**
 * PieceTableHelper will coordinate piece filtration and selection of tabel structures
 * 
 * Depending on the task the artist is engaged in, they may need to see different 
 * sub-sets of the available pieces and they may need different presentation and 
 * functionality for the pieces they see.
 * 
 * @author dondrake
 */
class PieceTableHelper extends Helper {
	
	/**
	 * 
	 * @param type $pieces
	 * @param type $filter_strategy
	 */
	public function render($pieces, $filter_strategy) {
		
	}
	
	/**
	 * define('PIECE_FILTER_COLLECTED', 'collected');
	 * define('PIECE_FILTER_NOT_COLLECTED', 'not_collected');
	 * define('PIECE_FILTER_ASSIGNED', 'assigned');
	 * define('PIECE_FILTER_NOT_COLLECTED', 'not_collected');* define('PIECE_FILTER_UNASSIGNED', 'not_assigned');
	 * define('PIECE_FILTER_FLUID', 'fluid');
	 */
	public function filterCollected($piece, $key = NULL) {
		return $piece->collected === 1;
	}
	
	public function filterNotCollected($piece, $key = NULL) {
		return $piece->collected !== 1;
	}
	
}

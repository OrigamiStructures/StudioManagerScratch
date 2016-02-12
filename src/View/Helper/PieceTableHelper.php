<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Collection\Collection;

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
	
	protected $_map = [
		PIECE_FILTER_COLLECTED => 'filterCollected',
		PIECE_FILTER_NOT_COLLECTED => 'filterNotCollected',
		PIECE_FILTER_ASSIGNED => 'filterAssigned',
		PIECE_FILTER_UNASSIGNED => 'filterUnassigned',
		PIECE_FILTER_FLUID => 'filterFluid',
		
	];
	
	/**
	 * 
	 * @param type $pieces
	 * @param type $filter_strategy
	 */
	public function render($pieces, $filter_strategy) {
		
	}
	
	public function filter($pieces, $filter_strategy) {
		$method = $this->_map[$filter_strategy];
		$filtered_pieces = (new Collection($pieces))->filter([$this, $method]);
		return $filtered_pieces;
	}
	
	public function filterCollected($piece, $key = NULL) {
		return $piece->collected === 1;
	}
	
	public function filterNotCollected($piece, $key = NULL) {
		return $piece->collected !== 1;
	}
	
	public function filterAssigned($piece, $key = NULL) {
		return is_null($piece->format_id);
	}
	
	public function filterUnassigned($piece, $key = NULL) {
		return $piece->collected !== 1;
	}
	
	public function filterFluid($piece, $key = NULL) {
		return $piece->disposition_count === 0;
	}
		
}

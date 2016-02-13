<?php
namespace App\Lib;

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
class PiecesUtility {
	
	protected $_map = [
		PIECE_FILTER_COLLECTED => 'filterCollected',
		PIECE_FILTER_NOT_COLLECTED => 'filterNotCollected',
		PIECE_FILTER_ASSIGNED => 'filterAssigned',
		PIECE_FILTER_UNASSIGNED => 'filterUnassigned',
		PIECE_FILTER_FLUID => 'filterFluid',
		PIECE_FILTER_NONE => FALSE,
		PIECE_SORT_NONE => FALSE,
		
	];
	
	protected $_state = [
		'edition' => [
			'filter' => PIECE_FILTER_UNASSIGNED,
			'sort' => PIECE_FILTER_NONE,
		],
		'format' => [
			'filter' => PIECE_FILTER_NONE,
			'sort' => PIECE_FILTER_NONE,],
	];


	/**
	 * 
	 * @param type $pieces
	 * @param type $filter_strategy
	 */
	public function render($pieces, $layer) {
		
	}
	public function filterStrategy($layer) {
		return $this->_map[$this->_state[$layer]['filter']];
	}
	
	public function filter($pieces, $layer) {
		$method = $this->filterStrategy($layer);
		if ($method) {
			$filtered_pieces = (new Collection($pieces))->filter([$this, $method])->toArray();
		} else {
			$filtered_pieces = $pieces;
		}
		return $filtered_pieces;
	}
	
	public function filterCollected($piece, $key = NULL) {
		return $piece->collected === 1;
	}
	
	public function filterNotCollected($piece, $key = NULL) {
		return $piece->collected !== 1;
	}
	
	public function filterAssigned($piece, $key = NULL) {
		return !is_null($piece->format_id);
	}
	
	public function filterUnassigned($piece, $key = NULL) {
		return is_null($piece->format_id);
	}
	
	public function filterFluid($piece, $key = NULL) {
		return $piece->disposition_count === 0;
	}
		
}

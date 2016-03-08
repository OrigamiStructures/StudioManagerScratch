<?php
namespace App\Lib;

use Cake\View\Helper;
use Cake\Collection\Collection;

/**
 * PieceTableHelper will coordinate piece filtration to support tabel structures
 * 
 * Depending on the task the artist is engaged in, they may need to see different 
 * sub-sets of the available pieces and they may need different presentation and 
 * functionality for the pieces they see.
 * 
 * @author dondrake
 */
class PiecesUtility {
	
	/**
	 * Map filter constants to actual method names
	 * 
	 * FILTERS WILL BE COMPLEX, ACCOUNTING FOR DISPOSITION DATES AND 
	 * DATES OF DISPOSITIONS ATTACHED TO THE PIECES BEING FILTERED.
	 * POSSIBLY: The basic filter will be done, then an second process will be 
	 * called to do date work? This would be fine if the dates were always a 
	 * subtractive process. But they may be additive, for example a piece 
	 * out on loan may be coming back for later storage.
	 * ANOTHER POSSIBLITY: A basic filter is done based on simple factors like 
	 * cache counter values. Then a new query is done on the excluded pieces 
	 * to get their dispos and do deeper analysis, keeping the complex work 
	 * to a minimum. 
	 * 
	 * And filters may work for other processes too like portfolio construction 
	 * and who knows what.
	 *
	 * @var array
	 */
	protected $_filter_map = [
		PIECE_FILTER_COLLECTED => 'filterCollected',
		PIECE_FILTER_NOT_COLLECTED => 'filterNotCollected',
		PIECE_FILTER_ASSIGNED => 'filterAssigned',
		PIECE_FILTER_UNASSIGNED => 'filterUnassigned',
		PIECE_FILTER_FLUID => 'filterFluid',
		PIECE_FILTER_RIGHTS => 'filterRights',
		PIECE_FILTER_NONE => FALSE,
	];
	protected $_sort_map = [
		PIECE_SORT_NONE => FALSE,
	];
	
	protected $_state = [
		'edition' => [
			'filter' => PIECE_FILTER_UNASSIGNED,
			'sort' => PIECE_SORT_NONE,
		],
		'format' => [
			'filter' => PIECE_FILTER_NONE,
			'sort' => PIECE_SORT_NONE,],
	];


	/**
	 * 
	 * @param type $pieces
	 * @param type $filter_strategy
	 */
//	public function render($pieces, $layer) {
//		
//	}
	
	
	public function chooseFilter($disposition_type) {
		return PIECE_FILTER_NONE;
	}

	public function filterStrategy($layer, $filter = FALSE) {
		if ($filter && in_array($filter, array_keys($this->_filter_map))) {
			$this->_state[$layer]['filter'] = $filter;
		} elseif ($filter) {
			throw new \BadMethodCallException('Unknown piece filter requested');
		}
		return $this->_filter_map[$this->_state[$layer]['filter']];
	}
	
	public function filter($pieces, $layer, $filter = FALSE) {
		$method = $this->filterStrategy($layer, $filter);
		
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

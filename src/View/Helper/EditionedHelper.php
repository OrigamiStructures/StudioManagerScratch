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
		$unassigned = $reassign = '';
		if ($edition->hasUnassigned()) {
			$grammar = $edition->unassigned_piece_count === 1 ?
				['is', $edition->unassigned_piece_count, 'piece', 'hasn\'t', ] :
				['are', $edition->unassigned_piece_count, 'pieces', 'haven\'t'];
			$unassigned = sprintf(
					"<p>There %s %d %s that %s been assigned to a format.</p>\n",
					$grammar[0], $grammar[1], $grammar[2], $grammar[3]);
			
		} else {
			// no statement iff all pieces are assigned
		}
		if ($edition->hasFluid() && $edition->format_count > 1) {
			$grammar = $edition->fluid_piece_count === 1 ?
				['is', $edition->fluid_piece_count, 'piece', 'can', ] :
				['are', $edition->fluid_piece_count, 'pieces', 'could'];
			$reassign = sprintf(
					"<p>There %s %d %s that %s be reassigned to different formats.</p>\n",
					$grammar[0], $grammar[1], $grammar[2], $grammar[3]);
		} else {
			// no statement if no pieces can be reassigned
		}
		
		echo $unassigned . $reassign;
	}
	
	protected function _editionPieceTools($edition) {
		$assignment_tool = '';
		if ($edition->hasUnassigned() || ($edition->hasFluid() && $edition->format_count > 1)) {
			$assignment_tool = $this->Html->link("Assign pieces to formats",
				['controller' => 'pieces', 'action' => 'review', '?' => [
					'artwork' => $edition->artwork_id,
					'edition' => $edition->id,
				]]) . "\n";
		}
		echo $assignment_tool;
	}
		
	protected function _formatPieceSummary($format) {
		$assigned = $reassign = $dispositions = $salable = '';
		
//		"There are x pieces assigned to the format, y of them can be reassigned.";
		if ($format->hasFluid()) {
			$count = $format->fluid_piece_count === $format->assigned_piece_count ?
					'All' : $format->fluid_piece_count;
			$reassign = sprintf('<br />%s of them can be reassigned to other formats.', 
					$count);
		}
		if ($format->hasAssigned()) {
			$grammar = $format->assigned_piece_count === 1 ?
				['is', $format->assigned_piece_count, 'piece', $reassign] :
				['are', $format->assigned_piece_count, 'pieces', $reassign];
			$assigned = sprintf("<p>There %s %d %s assigned to this format.%s</p>\n",
					$grammar[0], $grammar[1], $grammar[2], $grammar[3]);
		}
		
//		"There are status events recorded for t pieces";
		if ($format->hasDisposed()) {
			$count = $format->disposed_piece_count === $format->assigned_piece_count ?
					'all' : $format->disposed_piece_count;
			$grammar = ($count === 1) || ($count === 'all') ?
				[$count, 'of them'] :
				[$count, 'pieces'];
			$dispositions = sprintf("<p>There are status events recorded for %s %s.</p>\n",
					$grammar[0], $grammar[1]);
		}
		
//		"There are up to z pieces in this format available for sale. Examine these pieces";
		if ($format->hasSalable()) {
			
		}
		
		echo $assigned . $dispositions;
		
	}
	protected function _formatPieceTools($format) {
		"Add status information";
	}
	
}

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

	/**
	 * Limited/Open edition level reporting on quantity, unassigned, and reassignable
	 * 
	 * Quantity is reported in the Edition Title line through an entity getter
	 * 
	 * Limited/Open editions can vary on both the number of pieces and the 
	 * number of formats. At the Edition level we have 2 values to report on we 
	 * will generate 2 core statements in a few grammatical variations:
	 * 
	 * 1a - There are n pieces that have been assigned to a format.
	 * 1b - There is 1 piece that has been assigned to a format.
	 * 1b - No piece that have been assigned to any format.
	 * 
	 * 2a - There are n pieces that can be reassigned to different formats.
	 * 2a - There is 1 piece that can be reassigned to a different format.
	 * 2a - There are no pieces that can be reassigned.
	 * 
	 * Tool avaialbility for these conditions is also provided in this class
	 * 
	 * @param type $edition
	 */
	protected function _editionPieceSummary($edition) {
		$unassigned = $reassign = '';
		if ($edition->hasUnassigned()) {
			$grammar = $edition->unassigned_piece_count === 1 ?
				['is', $edition->unassigned_piece_count, 'piece', 'hasn\'t', ] :
				['are', $edition->unassigned_piece_count, 'pieces', 'haven\'t'];
			$unassigned = sprintf(
					"<p></p>\n",
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
		
	/**
	 * Limited/Open format level reporting on assigned, disposed, and salable
	 * 
	 * Limited/Open editions can vary on both the number of pieces and the 
	 * number of formats. At the Format level we  will generate 3 core statements:
	 * 
	 * 1a - There are no pieces assigned to this format.
	 * 1b - There are n pieces assigned to this format.
	 * 1c - There is 1 piece assigned to this format.
	 * 
	 * Conditionally on the existance of multiple formats:
	 * 1.1a - None of them can be reassigned to other formats.
	 * 1.1b - All  of them can be reassigned to other formats.
	 * 1.1c - N    of them can be reassigned to other formats.
	 * 1.1d - 1    of them can be reassigned to another format.
	 * 
	 * 2a - 'You haven\'t recorded the status of any pieces'
	 * 2c - The status of 1 piece has been recorded.
	 * 2b - There are status events recorded for n pieces.
	 * 2d - There are status events recorded for all the pieces.
	 * 
	 * 3a - There might be 1 piece   available for sale in this format.
	 * 3a - There are up to n pieces available for sale in this format.
	 * 3a - SOLD OUT
	 * 
	 * @param type $edition
	 */
	protected function _formatPieceSummary($format, $edition) {
		$assigned = $reassign = $dispositions = $salable = '';
		
//		"..., y of them can be reassigned.";
		if ($format->hasFluid() && $edition->format_count > 1) {
			if ($format->fluid_piece_count === $format->assigned_piece_count) {
				$count = 'All';
			} elseif ($format->fluid_piece_count === 0) {
				$count = 'None';
			} else {
				$count = $format->fluid_piece_count;
			}
			$reassign = sprintf('<br />%s of them can be reassigned to other formats.', 
					$count);
		}
		
//		"There are x pieces assigned to the format, ....";
		if ($format->hasAssigned()) {
			$count = $format->assigned_piece_count === 0 ? 'no' : $format->assigned_piece_count;
			$grammar = $format->assigned_piece_count === 1 ?
				['is', $count, 'piece', $reassign] :
				['are', $count, 'pieces', $reassign];
			$assigned = sprintf("<p>There %s %d %s assigned to this format.%s</p>\n",
					$grammar[0], $grammar[1], $grammar[2], $grammar[3]);
		} else {
			$assigned = "<p>This fomat has no pieces assigned.</p>\n";
		}
		
//		"There are status events recorded for t pieces";
		if ($format->hasDisposed()) {
			if ($format->disposed_piece_count === 1) {
				$dispositions = 'The status of 1 piece has been recorded.';
			} else {
				$count = $format->disposed_piece_count === $format->assigned_piece_count ?
						'all' : $format->disposed_piece_count;
				$grammar = ($count === 'all') ?
					"$count of them" :
					"$count pieces";
				$dispositions = sprintf("<p>There are status events recorded for %s.</p>\n",
						$grammar);
			}
			
		} elseif ($format->hasAssigned()) {
			$dispositions = 'You haven\'t recorded the status of any pieces.';
		}
		
//		"There are up to z pieces in this format available for sale";
		$fluid = $edition->fluid_piece_count;
		if ($format->hasSalable($fluid)) {
			$grammar = $format->salable_piece_count($fluid) === 1 ?
				'there might be 1 piece' :
				"There are up to {$format->salable_piece_count($fluid)} pieces";
			$salable = sprintf("<p>%s available for sale in this format.</p>\n",
					$grammar);
		} else {
			$salable = "<p class=\"sold_out\">SOLD OUT</p>\n";
		}
		
		echo $assigned . $dispositions . $salable;
		
	}
	protected function _formatPieceTools($format, $edition) {
		"Add status information";
	}
	
}

<?php
namespace App\View\Helper;

use App\View\Helper\EditionFactoryHelper;
use Cake\ORM\TableRegistry;


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
	 * 1a - No pieces have been assigned to any format.
	 * 1b - All pieces have been assigned to formats.
	 * 1c - There are n pieces not assigned to a format.
	 * 1d - There is 1 piece not assigned to a format.
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
			if ($edition->unassigned_piece_count === $edition->quantity) {
				$unassigned = 'No pieces have been assigned to any format.';				
			} else {
				$unassigned = $edition->unassigned_piece_count === 1 ?
					'There is one piece not assigned to a format.' :
					"There are {$edition->unassigned_piece_count} pieces not assigned to a format.";
			}
		} elseif ($edition->format_count > 1) {
			$unassigned = 'All pieces have been assigned to formats.';
		}

		if ($edition->hasFluid() && 
				($edition->format_count > 1) && 
				($edition->assigned_piece_count > 0)) 
		{
			$reassign = $edition->fluid_piece_count === 1 ?
				'There is one piece that can be reassigned to a different format.' :
				"There are $edition->fluid_piece_count that could be reassigned to different formats.";
		} elseif ($edition->assigned_piece_count > 0 && $edition->format_count > 1) {
			$reassign = 'There are no pieces that can be reassigned.';
		}
		
		echo "<p>$unassigned</p>\n<p>$reassign</p>\n";
	}
	
	/**
	 * Generate all the Edition layer tools for piece management
	 * 
	 * Currently there is only one tool at this layer:
	 * Generate tool/link to piece assignment page for an edition
	 * 
	 * Assignment and reassignment are managed from the same page. The tool 
	 * label will say either or both words as appropriate to the circumstance
	 * 
	 * @param Entity $edition
	 */
	protected function _editionPieceTools($edition) {
		$assignment_tool = '';
		if ($edition->hasUnassigned() || ($edition->hasFluid() && $edition->format_count > 1)) {
			
			if ($edition->hasUnassigned()) {
				$label[] = 'Assign';
			}
			if ($edition->hasFluid() && ($edition->fluid_piece_count !== $edition->unassigned_piece_count)) {
				$label[] = 'Reassign';
			}
			$label = implode('/', $label);
		
			$assignment_tool = $this->Html->link("$label pieces to formats",
				['controller' => 'editions', 'action' => 'assign', '?' => [
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
			if ($format->hasSalable($edition->undisposed_piece_count)) {
				$assigned = "<p>This format has no pieces assigned.</p>\n";
			} else {
				$assigned = "<p>This format was never implemented.</p>\n";
			}
			
		}
		
//		"There are status events recorded for t pieces";
		if ($format->hasDisposed()) {
			if ($format->disposed_piece_count === 1) {
				$dispositions = "<p>The status of 1 piece has been recorded.</p>\n";
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
			$dispositions = "<p>You haven't recorded the status of any pieces.</p>\n";
		}
		
//		"There are up to z pieces in this format available for sale";
		$undisposed = $edition->undisposed_piece_count;
		if ($format->hasSalable($undisposed)) {
			$grammar = $format->salable_piece_count($undisposed) === 1 ?
				'there might be 1 piece' :
				"There are up to {$format->salable_piece_count($undisposed)} pieces";
			$salable = sprintf("<p>%s available for sale in this format.</p>\n",
					$grammar);
		} else {
			if (!$format->hasAssigned()) {
				$salable = "";
			} else {
				$salable = "<p class=\"sold_out\">SOLD OUT</p>\n";
			}
			
		}
		
		echo $assigned . $dispositions . $salable;
		
	}

	/**
	 * Future size-change rules could be complicated:
	 * 
	 * edition->collected_piece_count set absolute minimum
	 * edition->disposed_piece_count sets current minimum
	 * 
	 * disposed zero, minimum 1
	 * collected zero, minimum disposed unless some can delete
	 * collected positive != disposed minimum is disposed unless
	 *  some can delete, but never less than collected.
	 * collected = quantity, sold out
	 * 
	 * if disposed < quantity show set to minumum check
	 * if disposed > 0 && disposed > collected show dispose tool
	 * always show new edition size input (placeholder: min or greater)
	 * 
	 * This edition is sold out.
	 * There N pieces sold.
	 * - Don't report zero sales
	 * 
	 * Current rules are simpler
	 * [Reduce or ]Increase the edition to (placehlder: min or greater)
	 * 
	 * 
	 * @param type $edition
	 * @return string
	 */
//	public function editionQuantitySummary($edition) {
//		$size_statement = $increase = '';
//		
//		if ($edition->type === EDITION_OPEN) {
//			$label = 'Increase the edition size to:';
//			$minimum = $edition->disposed_piece_count;
//			if (!$edition->hasSalable()) {
//				$sold_statement = "This edition is sold out.";
//			}
//			if ($edition->hasFluid()) {
//				$label = "Reduce or $label";
//			}
//			
//			
//			
//			// whatever its sold-out status, you can always make it bigger
//			$increase = 'Input to increase the edition size. ADD X PIECES TO THE EDITION';
//			
////			echo "<p>$size_statement<br />$increase</p>";
//			
//		} else {
//			// max numbered disposed piece is minimum edition size
//			// policy statement about limited editions
//			// new total 'size' of edition
//			return 'Message for limited editons';
//		}
//	}
	
	public function quantityInput($edition, $edition_index) {
		
		$form = $this->_View->loadHelper('Form');
		$EditionTable = TableRegistry::get('Editions');
		$minimum = $EditionTable->minimumSize($edition);

		$label = ($edition->hasFluid() || $edition->hasUnassigned() ? 'Reduce or ' : '') .
				"Change the edition size (minimum size $minimum):";
		$default = $edition->quantity;
		$policy_statement = '';

		if ($edition->type === EDITION_LIMITED & $edition->hasCollected()) {
			$pieces = $edition->collected_piece_count === 1 ?
					'one piece' :
					"$edition->collected_piece_count pieces";
			$policy = "You have sold $pieces from this edition.<br />Most artists "
					. "believe an edition's size should not be increased after active "
					. "sales have begun. ClearStudio does not prevent this practice. "
					. "Proceed according to your own policies.";
			$policy_statement = $this->Html->para('policy_statement', $policy) . "\n\t\t";
		}
		
		$output = $policy_statement . 
			 $form->input("editions.$edition_index.quantity", [
				'default' => $default, 
				'label' => $label
			]); 
		
		return $output;
		
	}

	/**
	 * Decide what piece table display is right for this edition and context
	 * 
	 * @param Entity $edition
	 */
	protected function _editionPieceTable($edition) {
		if (is_null($this->SystemState->artworks)) {
			
			// the filter strategy is assumed to have been set at this point . 
			// Seems like a lot of coupling. 
			$pieces = $this->pieceTool()->filter($edition->pieces, 'edition');
			
			if ($edition->hasUnassigned()) {
				$caption = 'Pieces in this edtion that haven\'t been assigned to a format.';
			} else {
				$caption = '';
//				// this information is already shown for empty editions
//				$caption = 'All the pieces in this edition are assigned to formats';
			}
			
			$providers = ['edition' => $edition];
			$this->_View->set(compact('caption', 'pieces', 'providers'));
		}		
	}

	/**
	 * Decide what piece table display is right for this format and context 
	 * 
	 * @param Entity $format
	 * @param Entity $edition
	 */
	protected function _formatPieceTable($format, $edition) {
		if (!is_null($this->SystemState->artworks)) {
			$caption = '';
			$this->_View->set('caption', $caption);
			return;
		}
		
//		osd($this->SystemState->standing_disposition);
//		osd($this->SystemState->controller() !== 'formats');die;
		
		// detecting when we're fully qualified to the format level has been uncertain. 
		// controller() can't be relied on because of redirecting that happens
		if ($this->SystemState->standing_disposition && $this->SystemState->isKnown('format')) {
			$this->_dispositionModeFormatPieceTable($format, $edition);
		} else {
			$this->_mainModeFormatPieceTable($format, $edition);
		}
	}
	
	/**
	 * Make variables that can support rendering the 'disposition' piece table
	 * 
	 * The disposition type is always known and we're clear down to a single 
	 * format display now. Show all the appropriate pieces for that format even 
	 * if they are not currently assigned. 
	 * 
	 * @param entity $format
	 * @param entity $edition
	 */
	private function _dispositionModeFormatPieceTable($format, $edition){
		$disposition = $this->SystemState->standing_disposition;
		
		$pieces = $this->pieceTool()
			->filter($edition->pieces, 'edition', $this->_chooseFilter($disposition->type));
		$caption = "Indicate the pieces you want to include in this {$this->DispositionTools->dispositionLabel($disposition)}";
		$providers = $this->_prepareProviders();
		
		$this->_View->set(compact('caption', 'pieces', 'providers'));
	}

	/**
	 * Prepare 'providers' object that can give the owner name for each piece in the edition
	 * 
	 * In processes that allow the assignment of pieces to other structures, a format 
	 * will be show with more than just its assigned pieces (in many cases). When 
	 * that happens, the artist needs to know where the piece is from so they can 
	 * decide whether to reassign it to this format and then to the new structure. 
	 * However, these views are rendered from data returned by ArtworkStack not 
	 * PieceAssignment, so we have to construct the knowledge base.
	 */
	private function _prepareProviders() {
		$EditionTable = TableRegistry::get('Editions');
		$conditions = $this->_View->SystemState->buildConditions([]);
		
		$provider_set = $EditionTable->get($this->_View->SystemState->queryArg('edition'), [
				'conditions' => $conditions,
				'contain' => ['Formats']]);
		return ['edition' => $provider_set] + $provider_set->formats;
	}


	private function _mainModeFormatPieceTable($format, $edition) {
		// the filter strategy is assumed to have been set at this point
		// and format->pieces is assumed to be the working set. Seems 
		// like a lot of coupling. And some bad assumptions.
		// possibly this is just falling through on default settings?
		$pieces = $this->pieceTool()->filter($format->pieces, 'format');

		if ($format->hasAssigned()) {
			$caption = 'Pieces in this format.';

		} else {
//				// this information is already shown for empty formats
//				$caption = 'No pieces have been assigned to this format.';
			$caption = '';
		}

		$providers = [$format];
		$this->_View->set(compact('caption', 'pieces', 'providers'));
	}

}

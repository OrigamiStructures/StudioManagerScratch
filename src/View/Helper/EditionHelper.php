<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\HtmlHelper;

/**
 * CakePHP EditionHelper
 * @author dondrake
 */
class EditionHelper extends Helper {
	
	public $helpers = ['Html'];


	public function editionPieceSummary($edition) {
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
	
	public function editionPieceTools($edition) {
		
	}
	
	public function editionPieceFields($edition) {
		
	}
	
	public function formatPieceSummary($edition) {
		
	}
	
	public function formatPieceTools($edition) {
		
	}
	
	public function formatPieceFields($edition) {
		
	}
	
	
}

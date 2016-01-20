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
	
	protected function _formatPieceSummary($format) {
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
	
	protected function _formatPieceTools($format) {
		$piece = $format->pieces[0];
		if ($this->_canDispose($piece)) {
			echo $this->Html->link("Add status information",
				['controller' => 'pieces', 'action' => 'create', '?' => [
					'format' => $format->id,
					'piece' => $piece->id
				]]);
		} else {
			echo $this->Html->tag('p', 
				'You can\'t change the status of this artwork.', 
				['class' => 'current_disposition']
			);
		}
	}
}
	
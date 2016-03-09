<?php
namespace App\Lib\Traits;

use Cake\View\Helper;
use Cake\Collection\Collection;
use App\Model\Entity\Disposition;

/**
 * PieceTableHelper will coordinate piece filtration to support tabel structures
 * 
 * Depending on the task the artist is engaged in, they may need to see different 
 * sub-sets of the available pieces and they may need different presentation and 
 * functionality for the pieces they see.
 * This class hold a bunch of simple filter callables that concrete rule classes 
 * can use. This class will decide which concrete rule class should handle the 
 * request, will make that object and pass itself as an argument
 * 
 * @author dondrake
 */
trait PieceFilterTrait {
	
	public function PiecesTable() {
		if (!isset($this->Pieces)) {
			$this->Pieces = \Cake\ORM\TableRegistry::get('Pieces');
		}
		return $this->Pieces;
	}
	
	public function filterCollected($piece, $key = NULL) {
		return $piece->collected === 1;
	}
	
	/**
	 * 
	 * @param type $piece
	 * @param type $key
	 * @return type
	 */
	public function filterNotCollected($piece, $key = NULL) {
		return $piece->collected === NULL || $piece->collected === 0;
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
	
	/**
	 * Is the piece free of disposition obligations as of $this->_target_date?
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 * @throws \BadMethodCallException
	 */
	public function filterAvailableOn($piece, $key = NULL) {
		if (!isset($this->target_date)) {
			throw new \BadMethodCallException('The \'target_date\' property must be set for this filter.');
		}
		
		if ($this->filterFluid($piece)) {
			return TRUE;
		}
		if (!isset($piece->dispositions)) {
			$proxy = $this->PiecesTable()->get($peice->id, ['contain' => ['Dispositions' => ['conditions' =>['start_date' > $this->target_date]]]]);
			osd($proxy->toArray());
		} else {
			$collection = new Collection($piece->dispositions);
			$future_obligations = $collection->filter([$this, 'filterFutureDispositions']);
			return $future_obligations->count() == 0;
		}
	}
		
	public function forSaleOnDate($piece, $key) {
		if (
				$this->filterNotCollected($piece) &&
				$this->filterAvailableOn($piece)
		) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

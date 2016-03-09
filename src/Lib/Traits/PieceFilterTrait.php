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
	
	/**
	 * Access point for the Pieces table
	 * 
	 * @return Table
	 */
	public function PiecesTable() {
		if (!isset($this->Pieces)) {
			$this->Pieces = \Cake\ORM\TableRegistry::get('Pieces');
		}
		return $this->Pieces;
	}
	
	/**
	 * Is the piece collected
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 */
	public function filterCollected($piece, $key = NULL) {
		return $piece->collected === 1;
	}
	
	/**
	 * Is the piece un-collected
	 * 
	 * It may have other dispositions, but it's not collected
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 */
	public function filterNotCollected($piece, $key = NULL) {
		return is_null($piece->collected);
	}
	
	/**
	 * Is the piece assigned to a format
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 */
	public function filterAssigned($piece, $key = NULL) {
		return !is_null($piece->format_id);
	}
	
	/**
	 * Is the piece unassigned.
	 * 
	 * These pieces are still 'virtual', belonging only to the edition
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 */
	public function filterUnassigned($piece, $key = NULL) {
		return is_null($piece->format_id);
	}
	
	/**
	 * Is the peice free of all dispositions?
	 * 
	 * This characteristic would make the piece reassignable, even if it 
	 * is currently assigned to a format
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 */
	public function filterFluid($piece, $key = NULL) {
		return $piece->disposition_count === 0;
	}
	
	/**
	 * Is the piece free of future disposition obligations as of $this->_target_date?
	 * 
	 * This does not account for disposition type. 
	 * It strictly filters according to date. 
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 * @throws \BadMethodCallException
	 */
	public function filterAvailableOnDate($piece, $key = NULL) {
		if (!isset($this->target_date)) {
			throw new \BadMethodCallException('The \'target_date\' property must be set for this filter.');
		}
		
		// this skips out if the piece has attached dispositions at all
		if ($this->filterFluid($piece)) {
			return TRUE;
		}
		
		if (!isset($piece->dispositions)) {
			
			// this works for pieces that don't have contained dispositions
			$proxy = $this->PiecesTable()->find()
				->where(['Pieces.id' => $piece->id])
				->contain(['Dispositions' => function ($q) {
					return $q->where(['end_date > ' => $this->target_date]);
				}])
				->first();
				
				return empty($proxy->toArray()['dispositions']);

		} else {
			
			// This works for pieces that have contained dispositions
			$collection = new Collection($piece->dispositions);
			$future_obligations = $collection->filter([$this, 'filterFutureDispositions']);
			return $future_obligations->count() == 0;
		}
	}
	
	/**
	 * Is the piece free of Unavailable type dispositions?
	 * 
	 * A single 'Unavailable' type disposition removes the piece from circulation 
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 * @throws \BadMethodCallException
	 */
	public function filterNotUnavailable($piece, $key = NULL) {
		// this skips out if the piece has attached dispositions at all
		if ($this->filterFluid($piece)) {
			return TRUE;
		}
		
		if (!isset($piece->dispositions)) {
			
			// this works for pieces that don't have contained dispositions
			$proxy = $this->PiecesTable()->find()
				->where(['Pieces.id' => $piece->id])
				->contain(['Dispositions' => function ($q) {
					return $q->where(['type' => DISPOSITION_UNAVAILABLE]);
				}])
				->first();
				
				return empty($proxy->toArray()['dispositions']);

		} else {
			
			// This works for pieces that have contained dispositions
			$collection = new Collection($piece->dispositions);
			$unavailable = $collection->filter([$this, 'filterUnavailableDispositions']);
			return $future_obligations->count() == 0;
		}
	}
	
	/**
	 * Is the disposition in the future relative to $this->target_date?
	 * 
	 * @param object $disposition
	 * @param string $key
	 * @return boolean
	 */
	public function filterUnavailableDispositions($disposition, $key) {
		return $disposition->type == DISPOSITION_UNAVAILABLE;
	}
	
	/**
	 * Is the piece free to be sold on $this->target_date
	 * 
	 * If the piece has any dispostion obligations in the future, it will 
	 * be disqualified from the sale set
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 */
	public function forSaleOnDate($piece, $key) {
		if (
				$this->filterNotCollected($piece) &&
				$this->filterAvailableOnDate($piece) &&
				$this->filterNotUnavailable($piece)
		) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

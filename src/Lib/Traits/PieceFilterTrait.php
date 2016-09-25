<?php
namespace App\Lib\Traits;

use App\Lib\Traits\DispositionFilterTrait;
use Cake\View\Helper;
use Cake\Collection\Collection;
use App\Model\Entity\Disposition;
use App\Lib\SystemState;

/**
 * PieceFilterTrait provides yes/no tests for Piece Entities
 * 
 * These filters can be used by iterators that are working on piece collections. 
 * These simple tests will return pieces that satisfy the filter method name 
 * (eg: ::filterUncollected() will return the piece if it has no dispositions of
 * type = 'collected'). The rejected pieces will be available in an array on 
 * $this->rejects(), indexed by the piece->id. Each rejected piece will have 
 * the reason(s) for its rejection recorded in piece->rejection_reason with a \n 
 * between each reason if there is more than one.
 * 
 * 
 * The filters work as the callable argument for a Filter (or other) Iterators. 
 * They expect Entity $piece and $key (the orinating array's index for the entry). 
 * 
 * <pre>
 * $collection = new Collection($edition->pieces);
 * $fluid = $collection->filter([$this, 'filterFluid'])->toArray();
 * $not_fluid = $this->rejects();
 * </pre>
 * 
 * Many of the filters are simple state-checks (eg: if($piece->isAssigned()){} )
 *  
 * A more complex variety of filter will create a disposition iterator for the 
 * piece and pass/fail the piece based on disposition examination.
 * 
 * More advanced filters require start and end dates be set so deeper analysis 
 * of time based dispositions can be done. 
 * 
 * Some filters will combine other simpler filters to implement complex piece 
 * selection rules.
 * 
 * @author dondrake
 */
trait PieceFilterTrait {
	
	use DispositionFilterTrait;
    
    /**
     * Rejected pieces collection array
     * 
     * @var array
     */
    protected $_rejected = [];
	
    /**
     * Process and store the pieces that fail individual tests
     * 
     * @param object $piece
     * @param int $key
     * @param boolean $result
     */
    protected function _reject($piece, $key, $reason) {
        $piece->rejection_reason = (!isset($piece->rejection_reason) ? '' : "\n") . $reason;
        $this->_rejected[$piece->id] = $piece;
		return FALSE;
    }
	
	/**
	 * Get the rejected pieces
	 * 
	 * Each rejected includes at least one reason for its rejection 
	 * at piece->rejection_reason
	 * 
	 * @param boolean $reset Reset the results array
	 * @return array
	 */
	public function rejects($reset = FALSE) {
		if ($reset) {
			$this->_rejected = [];
		}
		return $this->_rejected;
	}
	
	/**
	 * Is the piece collected
	 * 
	 * @param entity $piece
	 * @param string $key 
	 * @return boolean
	 */
	public function filterCollected($piece, $key = NULL) {
        if ($piece->isCollected()) {
			osd($piece->isCollected()); osd($piece);die;
			return TRUE;
		} else {
			return $this->_reject($piece, $key, 'Already collected');
		}
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
		if (!$piece->isCollected()) {
			return TRUE;
		} else {
			return $this->_reject($piece, $key, 'Collected');
		}
   	}
	
	/**
	 * Is the piece assigned to a format
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 */
	public function filterAssigned($piece, $key = NULL) {
		if ($piece->isAssigned()) {
			return TRUE;
		} else {
			return $this->_reject($piece, $key, 'Not assigned to a format');
		}
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
		if (!$piece->isAssigned()) {
			return TRUE;
		} else {
			return $this->_reject($piece, $key, 'Assigned to a format');
		}
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
		if ($piece->isFluid()) {
			return TRUE;
		} else {
			$s = $piece->disposition_count > 1 ? 's' : '';
			return $this->_reject($piece, $key, "Has $piece->disposition_count disposition$s");
		}
	}
	
	/**
	 * Does the piece have some disposition
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 */
	public function filterNotFluid($piece, $key = NULL) {
		if (!$piece->isFluid()) {
			return TRUE;
		} else {
			return $this->_reject($piece, $key, 'Is fluid');
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
		if ($piece->disposition_count === 0) {
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
				
				$result = empty($proxy->toArray()['dispositions']);

		} else {
			
			// This works for pieces that have contained dispositions
			$collection = new Collection($piece->dispositions);
			$unavailable = $collection->filter([$this, 'filterUnavailableDispositions']);
			$result = iterator_count($unavailable) == 0;
		}
        $this->_reject($piece, $key, $result, "Unavailable through damage or loss");
        return $result;
	}
	
	/**
	 * Do all existing dispositions allow the piece to proceed?
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 */
	public function filterDispositions($piece, $key) {
		$dispositions = $piece->dispositions;
		$collection = new Collection($dispositions);
		$unavailable = $collection->filter(function($disposition, $key){
			return in_array($disposition->type, SystemState::scrappedDispositionTypes());
		});
		
		if ((boolean) iterator_count($unavailable)) {
			return TRUE;
		} elseif ($record) {
			return $this->_reject($piece, $key, 'Available');
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Is the piece free of future disposition obligations as of $this->_start_date?
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
		if (!isset($this->start_date)) {
			throw new \BadMethodCallException('The \'start_date\' property must be set for this filter.');
		}
		
		// this skips out if the piece has attached dispositions at all
		if ($piece->disposition_count === 0) {
			return TRUE;
		}
		
//		if (!isset($piece->dispositions)) {
//			
//			// this works for pieces that don't have contained dispositions
//			// 09/2016 - This probably doesn't run any more because I changed 
//			// $piece->dispositions so it will always populate the property
//			$proxy = $this->PiecesTable()->find()
//				->where(['Pieces.id' => $piece->id])
//				->contain(['Dispositions' => function ($q) {
//					return $q->where(['end_date > ' => $this->start_date]);
//				}])
//				->first();
//				
//            $result = empty($proxy->toArray()['dispositions']);
//
//		} else {
			
			// This works for pieces that have contained dispositions
		$collection = new Collection($piece->dispositions);
		$future_obligations = $collection->filter([$this, 'filterFutureDispositions']);

		if (iterator_count($future_obligations) == 0) {
			return TRUE;
		} else {
			return $this->_reject($piece, $key, "Unavailable on $this->start_date");
		}
//		}
        
	}
	
	/**
	 * Is Disposition unavailable 
	 * 
	 * @param type $entity
	 * @param type $piece
	 * @return boolean
	 * @throws \BadMethodCallException
	 */
	public function filterFutureDispositions($entity) {
		osd(func_get_args());
//		$piece->reason = 'arbitrary';
		return;
		if (!isset($this->start_date)) {
			throw new \BadMethodCallException('The \'start_date\' property must be set for this filter.');
		}
		
		
		osd($this->start_date);
		osd($entity);
		return;
	}

	/**
	 * Is the piece free to be sold on $this->start_date
	 * 
	 * If the piece has any dispostion obligations in the future, it will 
	 * be disqualified from the sale set
	 * 
	 * @param entity $piece
	 * @param string $key
	 * @return boolean
	 */
	public function forSaleOnDate($piece, $key) {
		
		// no dispos is an automatic pass
		if ($this->filterFluid($piece, $key)) {
			return TRUE;
			
		// already is or scheduled for collection is automatic fail
		} elseif ($this->filterCollected($piece, $key)) {
			return FALSE;
		}
		
		// must be available and not required for future event
		$DispositionFilter = new \App\Lib\DispositionFilter();
		$DispositionFilter->addFilter('filterAvailable')
				->addFilter('filterNotFutureEvent');
		$disposition = $piece->dispostions;
		
		

		
		$DispositionFilter->runFilter(new Disposition(), 0);

	}
	
	public function forLoanInDateRange($piece) {
		
	}
}

<?php
namespace App\Lib;

use Cake\View\Helper;
use Cake\Collection\Collection;
use App\Model\Entity\Disposition;
use App\Lib\Traits\PieceFilterTrait;

/**
 * DispositionPieceFilter returns pieces that can receive a given disposition
 * 
 * If this class wasn't selected by a string assembly factory, it might better 
 * be called DispositionCandidateRules.
 * 
 * There is a need to identify which pieces are valid canditates to recieve 
 * a disposition. The range of possible disposition types and the history of 
 * the individual pieces make this a complex rule problem. This class implements 
 * the rules for all cases.
 * 
 * It has one public call point that requires two arguments:
 *		::filter($data, $disposition)
 * 
 * $data will carry the pieces (as documented on the method).
 * $disposition properities will be used to choose the rules to apply. 
 * 
 * Remeber that Dispostion entities have two 'type' columns:
 *		->type = general type, Transfer, Loan, etc
 *		->label = specific name of the event, Sale, Consignment, etc.
 * 
 * The disposition->type is used as a factory parameter to determine which 
 * method to call. All labeled varieties of each type will follow the same rules 
 * so disposition->label is not used in the factory process. 
 * 
 * The factory method is chosen by a switch statement on $dispostion->type and 
 * the method name is looked up in the _filter_map array.
 * 
 * 
 * 
 * @author dondrake
 */
class DispositionPieceFilter {
	
	use PieceFilterTrait;
	
	public $start_date;
	public $end_date;

	/**
	 * Map filter constants to actual method names
	 * 
	 * FILTERS WILL BE COMPLEX, ACCOUNTING FOR DISPOSITION DATES AND 
	 * DATES OF DISPOSITIONS ATTACHED TO THE PIECES BEING FILTERED.
	 * POSSIBLY: The basic filter will be done, then an second process will be 
	 * called to do date work? This would be fine if the dates were always a 
	 * subtractive process. But they may be additive, for example a piece 
	 * out on loan may be coming back for later storage.
	 * 
	 * !!!!!
	 * GOOD IDEA
	 * !!!!!
	 * 
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
		PIECE_FILTER_FOR_SALE_ON_DATE => '_forSaleOnDate',
		PIECE_FILTER_LOAN_FOR_RANGE => '_forLoanInDateRange',
		PIECE_FILTER_ASSIGNED => 'filterAssigned',
		PIECE_FILTER_UNASSIGNED => 'filterUnassigned',
		PIECE_FILTER_FLUID => 'filterFluid',
		PIECE_FILTER_RIGHTS => 'filterRights',
		PIECE_FILTER_NONE => FALSE,
	];

	/**
	 * 
	 * 
	 * @param mixed $data
	 * @param object $disposition
	 * @return array
	 */
	public function filter($data, $disposition) {
		
		// some filter rules examine dates
		$this->start_date = $disposition->start_date;
		$this->end_date = $disposition->end_date;

		$method = $this->_chooseDispositionFilter($disposition);
		$pieces = $this->$method($data, $disposition);
		
		return $pieces;
	}
		
	/**
	 * Choose a filter to determine availability for disposition
	 * 
	 * All except the first case appear to be stubs
	 * 
	 * @param type $disposition
	 * @return type
	 */
	protected function _chooseDispositionFilter($disposition) {
		switch ($disposition->type) {
			case DISPOSITION_TRANSFER:
				return $this->_filter_map[PIECE_FILTER_FOR_SALE_ON_DATE];
				break;
			case DISPOSITION_LOAN:
				return $this->_filter_map[PIECE_FILTER_LOAN_FOR_RANGE];
				break;
			case DISPOSITION_STORE:
				return $this->_filter_map[PIECE_FILTER_IN_STUDIO_ON_DATE];
				break;
			case DISPOSITION_UNAVAILABLE:
				return $this->_filter_map[PIECE_FILTER_EXTANT];
				break;
			
			// ????????
			// what are the rules for Rights pieces. They can get multiple 
			// dispositions, can't they?
			

			default:
				break;
		}
	}

	/**
	 * Get pieces that can be sold as of the provided date
	 * 
	 * These will be pieces that are assigned to a specific format or may be 
	 * reassigned to that format which also have satisfied all their disposition 
	 * obligations by the date specified in the disposition.
	 * 
	 * If $entity is a format, only its currently assigned pieces will be 
	 * returned. If $entity is an edition, then and reassignable piece 
	 * will be evaluated.
	 * 
	 * @param Entity $entity
	 * @param object $disposition
	 */
	protected function _forSaleOnDate($entity, $disposition) {
		osd('reroute');
		$this->start_date = $disposition->start_date->i18nFormat('yyyy-MM-dd');
//        osd($this->start_date, 'target date');
//        osd($entity->pieces, 'entity->pieces');
		$collection = new Collection($entity->pieces);
		$pieces = $collection->filter([$this, 'forSaleOnDate'])->toArray();
		
		// because of collection lazy-loading of data, toArray() must happen before unset!!
		unset($this->start_date);
		
		return $pieces;
	}
	
	protected function _forLoanInDateRange($entity, $disposition) {
		
	}
	
}

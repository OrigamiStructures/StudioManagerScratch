<?php
namespace App\Lib;

use Cake\View\Helper;
use Cake\Collection\Collection;
use App\Model\Entity\Disposition;
use App\Lib\Traits\PieceFilterTrait;

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
class DispositionPieceFilter {
	
	use PieceFilterTrait;
	
	public $target_date;

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
		PIECE_FILTER_FOR_SALE_ON_DATE => '_forSaleOnDate',
		PIECE_FILTER_ASSIGNED => 'filterAssigned',
		PIECE_FILTER_UNASSIGNED => 'filterUnassigned',
		PIECE_FILTER_FLUID => 'filterFluid',
		PIECE_FILTER_RIGHTS => 'filterRights',
		PIECE_FILTER_NONE => FALSE,
	];

	/**
	 * 
	 * @param mixed $data
	 * @param object $disposition
	 * @return array
	 */
	public function filter($data, $disposition) {
		$method = $this->_chooseDispositionFilter($disposition);
		$pieces = $this->$method($data, $disposition);
		
		return $pieces;
	}
		
	protected function _chooseDispositionFilter($disposition) {
		switch ($disposition->type) {
			case DISPOSITION_TRANSFER:
				return $this->_filter_map[PIECE_FILTER_FOR_SALE_ON_DATE];
				break;
			case DISPOSITION_LOAN:
				return $this->_filter_map[PIECE_FILTER_FOR_SALE_ON_DATE];
				break;
			case DISPOSITION_STORE:
				return $this->_filter_map[PIECE_FILTER_FOR_SALE_ON_DATE];
				break;
			case DISPOSITION_UNAVAILABLE:
				return $this->_filter_map[PIECE_FILTER_FOR_SALE_ON_DATE];
				break;

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
		$this->target_date = $disposition->start_date->i18nFormat('yyyy-MM-dd');
//        osd($this->target_date, 'target date');
//        osd($entity->pieces, 'entity->pieces');
		$collection = new Collection($entity->pieces);
		$pieces = $collection->filter([$this, 'forSaleOnDate'])->toArray();
		
		// because of collection lazy-loading of data, toArray() must happen before unset!!
		unset($this->target_date);
		
		return $pieces;
	}
}

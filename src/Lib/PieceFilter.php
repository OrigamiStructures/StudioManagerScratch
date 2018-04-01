<?php
namespace App\Lib;

use Cake\View\Helper;
use Cake\Collection\Collection;
use App\Model\Entity\Disposition;
use App\Lib\Traits\PieceFilterTrait;

/**
 * PieceFilter will coordinate piece filtration to support tabel structures
 * 
 * Depending on the task the artist is engaged in, they may need to see different 
 * sub-sets of the available pieces and they may need different presentation and 
 * functionality for the pieces they see.
 * 
 * This class selects a concrete class that knows the data structures and 
 * filtering rules for the current situation. 
 * 
 * @author dondrake
 */
class PieceFilter {
	
	use PieceFilterTrait;
	    
	protected $_start_date = FALSE;
	
	protected $_end_date = FALSE;
	
	/**
	 * Setter, handles start or end dates
	 * 
	 * @param string $name any name with 's|Start or e|End 
	 * @param mixed $value Time object or string to create one
	 * @return mixed False or Time object
	 */
	public function __set($name, $value) {
		if (preg_match('/start/i', $name)) {
			$name = '_start_date';
		} elseif (preg_match('/end/i', $name)) {
			$name = '_end_date';
		} else {
			return FALSE;
		}
		
		if (is_object($value) && get_class($value) === 'Cake\I18n\Time') {
			$this->$name = $value;
		} elseif (is_string($value)) {
			$this->$name = new \Cake\I18n\Time($value);
		} else {
			return FALSE;
		}
		
		return $this->$name;
		
	}

	/**
	 * Return some set of Piece Entities contained in the composite structure $pieces
	 * 
	 * If a $filter object is provided, that will determine both the class that 
	 * will handle the request, and what filtering strategy that class uses. 
	 * If none is provided, all the Piece Entities will be extracted and returned. 
	 * 
	 * @param array $pieces
	 * @param mixed $filter
	 * @return array
	 */
	public function filter($pieces, $filter = FALSE) {
		$this->rejected(CLEAR);
		
		if (is_object($filter) && get_class($filter) === 'App\Model\Entity\Disposition') {
			// 
			$disposition = $filter;
			$this->_start_date = $disposition->start_date;
			$this->_end_date = $disposition->end_date;
			$filter = $this->_chooseFilter($disposition->type);

		} elseif (is_string($filter) && method_exists($this, $filter)) {
			// in this case everything is set properly

		} else {
			$filter = FALSE;
			$valid_pieces = $pieces;
		} 
		
		if ((boolean) $filter) {
			$valid_pieces = new Collection($pieces);
			$valid_pieces->filter([$this, $filter]);
		} else {
			$valid_pieces = $pieces;
		}
				
		if (is_array($valid_pieces)) {
			return $valid_pieces;
		} else {
			return $valid_pieces->toArray();
		}
		
		//return is_array($valid_pieces) ? $valid_pieces : $valid_pieces->toArray();
	}
    
	/**
	 * Return the pieces that failed the filter
	 * 
	 * Passing TRUE will reset the array. 
	 * The array will only contain a valid data set after filter() is run. 
	 * 
	 * @param boolean $reset
	 * @return array
	 */
    public function rejected($reset = FALSE) {
		if ($reset) {
			$this->_rejected = [];
		}
        return $this->_rejected;
    }
	
	
	/**
	 * Choose a filter method based on a string that sent from the context
	 * 
	 * The public filter method can be told directly what filter to use. But 
	 * sometimes an object (like a disposition entity) will be sent and that 
	 * will provide context which will determine what filter to use.
	 * 
	 * In these cases, filter() prepares a string that describes the context 
	 * and this becomes the 'switch' used to select the filter method
	 * 
	 * @param string $switch some context indicator
	 * @return string filter method name
	 */
	protected function _chooseFilter($switch) {
		switch ($switch) {
			// switches on disposition->type
			case DISPOSITION_TRANSFER:
				return 'forSaleOnDate';
				break;
			case DISPOSITION_LOAN:
				return 'forLoanInDateRange';
				break;
			case DISPOSITION_STORE:
				return 'inStudioOnDate';
				break;
			case DISPOSITION_UNAVAILABLE:
				return 'isExtant';
				break;
			// ????????
			// what are the rules for Rights pieces. They can get multiple 
			// dispositions, can't they?
			
			// switches on other strings (none defined yet)
			default:
				break;
		}
	}
	
}

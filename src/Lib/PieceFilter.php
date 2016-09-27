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
	
	protected $Pieces;
    
    public $FilterClass;

	public function __construct() {
		$this->Pieces = \Cake\ORM\TableRegistry::get('Pieces');
	}
	
	/**
	 * Return some set of Piece Entities contained in the composite structure $pieces
	 * 
	 * If a $filter object is provided, that will determine both the class that 
	 * will handle the request, and what filtering strategy that class uses. 
	 * If none is provided, all the Piece Entities will be extracted and returned. 
	 * 
	 * @param mixed $pieces
	 * @param object $filter
	 * @return array
	 */
	public function filter($pieces, $filter) {
		if (is_object($filter)) {
			$this->FilterClass = $this->_selectRuleClass($filter);
			$valid_pieces = $this->FilterClass->filter($pieces, $filter);
		} elseif (is_string($filter) && method_exists($this, $filter)) {
			$valid_pieces = new Collection($pieces);
			$valid_pieces->filter([$this, $filter]);
		} else {
			$valid_pieces = $this->_extractPieces($pieces);
//			throw new \BadMethodCallException('Unknown filter method');
		}
		
		return $valid_pieces;
	}
    
    public function rejected() {
        return $this->FilterClass->rejects();
    }
	
	/**
	 * Select a filter class appropriated to a provided context class
	 * 
	 * This assumes there is a whole suite of xPieceFilter classes ready to 
	 * handle things for named classes of... what? Right now the only valid 
	 * entry that comes here is 'Disposition'. But I don't know of another 
	 * case where a different class would be provided. 
	 * 
	 * @param object $filter
	 * @return \App\Lib\filter_class
	 */
	protected function _selectRuleClass($filter) {
		$namespace = '\App\Lib\\';
		$segments = explode('\\', get_class($filter));
		$filter_class = array_pop($segments);
		$filter_class = "{$namespace}{$filter_class}PieceFilter";
		
		return new $filter_class;
	}
	
	/**
	 * Given an arbitrary composite structure, return the Piece entities it contains
	 * 
	 * @param mixed $data
	 * @param array $pieces
	 * @return array
	 */
	protected function _extractPieces($data, $pieces = []) {
		if (is_object($data)) {
			if (get_class($data) == 'App\Model\Entity\Piece') {
				$pieces[] = $data;
				return $pieces;
			}
			if (method_exists($data, 'toArray')){
				$data = $data->toArray();
			}
		}
		foreach ($data as $key => $node) {
			if ($key === 'pieces') {
				$nodes = $this->Pieces->newEntities($node);
				$pieces = array_merge($pieces, $nodes);
				return $pieces;
			}
			if (is_object($node) || is_array($node)) {
				$pieces = $this->_extractPieces($node, $pieces);
			}
		}
		return $pieces;
	}
	
}

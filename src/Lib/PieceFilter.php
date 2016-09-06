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
	 * Return some set of Piece Entities contained in the composite structure $data
	 * 
	 * If a $context object is provided, that will determine both the class that 
	 * will handle the request, and what filtering strategy that class uses. 
	 * If none is provided, all the Piece Entities will be extracted and returned. 
	 * 
	 * @param mixed $data
	 * @param object $context
	 * @return array
	 */
	public function filter($data, $context = 'none') {
		if (is_object($context)) {
			$this->FilterClass = $this->_selectRuleClass($context);
			$pieces = $this->FilterClass->filter($data, $context);
		} else {
			$pieces = $this->_extractPieces($data);
		}
		
		return $pieces;
	}
    
    public function rejected() {
        return $this->FilterClass->rejected;
    }
	
	protected function _selectRuleClass($context) {
		$namespace = '\App\Lib\\';
		$segments = explode('\\', get_class($context));
		$context_class = array_pop($segments);
		$filter_class = "{$namespace}{$context_class}PieceFilter";
		
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

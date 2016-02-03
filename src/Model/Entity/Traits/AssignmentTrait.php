<?php
namespace App\Model\Entity\Traits;

use Cake\Utility\Inflector;
use App\Lib\Range;
use Cake\Collection\Collection;
use App\Lib\SystemState;

/**
 * AssignmentTrait provides a common Piece access interface for Editions and Formats
 * 
 * Piece assignment processes require access to pieces of various categories 
 * which are children of both Edition and Format entities. This trait normalizes 
 * things across the two classes.
 *
 * @author dondrake
 */
trait AssignmentTrait {
		
	/**
	 * Return a 'number range' string or a count of pieces
	 * 
	 * Lists of numbered edition pieces can be described with typical 
	 * 1-7, 9, 12-13 style strings. Open edition pieces can only report
	 * on the count of pieces in the collection. 
	 * 
	 * @param \App\Model\Entity\Traits\Collection $pieces
	 * @param string $type The edition type
	 */
	public function range(Collection $pieces, $type) {
		if (in_array($type, SystemState::ltd())) {
			$numbers = $pieces->reduce(function($accumulate, $piece) {
				$accumulate[] = $piece->number;
				return $accumulate;
			}, []);
			$range = Range::constructRange($numbers, '{n}');
		} else {
			$range = $pieces->sumOf(function ($piece) {
				return $piece->quantity;
			});
		}
		return $range;
	}
	
	/**
	 * Return the assignable pieces in this entity
	 * 
	 * @param boolean $type Return value type, collection or array of entities
	 * @return array|Collection
	 * @throws \CakeDC\Users\Exception\BadConfigurationException
	 */
	public function assignablePieces($return_type = PIECE_COLLECTION_RETURN) {
		if (stristr($this->_className, 'Edition')) {
			$property = 'unassigned';
		} elseif (stristr($this->_className, 'Format')) {
			$property = 'fluid';
		} else {
			throw new \CakeDC\Users\Exception\BadConfigurationException(
				"{$this->_className} does not have assignable pieces, so it "
				. "is not compatible with the AssignmentTrait.");
		}
		return $this->_pieces($property, $return_type);
	}
	
	/**
	 * Unified interface for varied objects
	 * 
	 * @return boolean
	 * @throws \CakeDC\Users\Exception\BadConfigurationException
	 */
	public function hasAssignable() {
		if (stristr($this->_className, 'Edition')) {
			return $this->hasUnassigned();
		} elseif (stristr($this->_className, 'Format')) {
			return $this->hasFluid();
		} else {
			throw new \CakeDC\Users\Exception\BadConfigurationException(
				"{$this->_className} does not have assignable pieces, so it "
				. "is not compatible with the AssignmentTrait.");
		}
	}
	
	/**
	 * Return the values at the named property as entities or a collection
	 * 
	 * @param string $property
	 * @param boolean $return_type
	 * @return Collection|array
	 */
	protected function _pieces ($property, $return_type) {
		if ($return_type) {
			return new Collection($this->$property);
		} else {
			return $this->$property;
		}
	}

}

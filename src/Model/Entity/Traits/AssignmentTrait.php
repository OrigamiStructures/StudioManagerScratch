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
	 * 
	 * @param \App\Model\Entity\Traits\Collection $pieces
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
	 * 
	 * @param type $type
	 * @return \App\Model\Entity\Traits\Collection
	 * @throws \CakeDC\Users\Exception\BadConfigurationException
	 */
	public function assignablePieces($type = ASSIGNABLE_COLLECTION) {
		if (stristr($this->_className, 'Edition')) {
//			osd($this);die;
			$property = 'unassigned';
		} elseif (stristr($this->_className, 'Format')) {
			$property = 'fluid';
		} else {
			throw new \CakeDC\Users\Exception\BadConfigurationException(
				"{$this->_className} does not have assignable pieces, so it "
				. "is not compatible with the AssignmentTrait.");
		}
//		osd($this->$property);die;
		if (ASSIGNABLE_COLLECTION) {
			return new Collection($this->$property);
		} else {
			return $this->$property;
		}
	}

}

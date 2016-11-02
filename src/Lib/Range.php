<?php
namespace App\Lib;

use Cake\Utility\Hash;
use ArrayIterator;
/**
 * Description of Range
 *
 * @author dondrake
 */
class Range {

	/**
	 * Build a range string from an element in an array
	 * 
	 * Provide an array and a Hash style path to a node 
	 * where the values found at that node will describe an 
	 * ascending number sequence, this will make a range string 
	 * in the form "1-5, 7, 9, 12-33, 44"
	 * 
	 * array(1, 2, 3, 99) $path='{n}' range='1-3, 99'
	 * array(array('id'=>7), array('id'=>13) $path='{n}.id' range='7, 13'
	 * 
	 * See Hash::extract for more detail
	 * 
	 * @param array $data
	 * @param string $path
	 * @return string
	 */
	static function constructRange($data = array(), $path = '') {
		if (!is_array($data)) {
			$data = array();
		}
		$numbers = Hash::extract($data, $path);
		foreach ($numbers as $index => $value) {
			$numbers[$index] = intval($value);
		}
		$list = new ArrayIterator($numbers);
		
		$range = $previous = FALSE;
		while ($list->valid()) {
			
			// if this is the first entry
			if (!$range) {
				$range = $previous = $list->current();
				$list->next();
				continue;
			}
			
			// if this is the next number in a sequence
			if ($list->current() === ($previous + 1)) {
				switch ($range[strlen($range)-1]) {
					case '-':
						break;
					default:
						$range .= '-';
						break;
				}
				
			// if we jumped more than one number
			} else {
				switch ($range[strlen($range)-1]) {
					case '-':
						$range .= "$previous, {$list->current()}";
						break;
					default:
						$range .= ", {$list->current()}";
						break;
				}
			}

			$previous = $list->current();
			$list->next();
		}
		
		// check to see if we left a range unfinished
		if ($range[strlen($range)-1] === '-') {
			$range .= $previous;
		}
		
		return $range;
	}
	
	/**
	 * Turn a range string into an array with the sequence values
	 * 
	 * format: x-y, z, r, i-j
	 * 
	 * @param string $range
	 * @return array
	 */
	static function parseRange($range) {
		
		$sequence = array();
		
		/**
		 * this is being done by form validation so, i'm suppressing for now
		 */
//		$pattern = '/(\d+-\d+|\d+)(, *(\d+-\d+|\d+))*/'; 
//		preg_match($pattern, $range, $match); 
//		if ($range !== $match[0]) {
//			return $sequence;
//		}
		// this was the best validation prior to the regex above
//		if ((!is_int($range)) && (!is_string($range)) || (trim($range, ' ') == '')) {
//			return $sequence;
//		}
		
		// break on the range gaps first
		$groups = explode(',', $range);
		
		foreach ($groups as $series) {
			
			// if the group is a series x-y
			if (stristr($series, '-')) {
				$s = explode('-', $series);
				$series = range($s[0], $s[1]);
				$sequence = array_merge($sequence, $series);

			// otherwise its just a value 
			} else {
				$sequence[] = intval($series);
			}
		}
		
		// filter out the duplicates
		return array_flip(array_flip($sequence));
	}
	
}

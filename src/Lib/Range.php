<?php

namespace App\Lib;

use Cake\Utility\Hash;
use ArrayIterator;

/**
 * Range provides a shorthand way of describing sets of numbered pieces
 * 
 * It can understand shorthand notation and return the proper array of 
 * items or generate shorthand notation which describes an array.
 *
 * @todo How can this handle non-numeric piece labeling schemes?
 * There will have to be a lot of new abstraction to make this happen. 
 * We'll have to decide if we want to allow:
 * 	 a finite set of numbering options 
 *   user requested options that we then incorporate
 *   user created options
 * The second strategy seems best-ish. It would keep user freedom high and 
 * user tech-savvy low. 
 * We would probably need a config file that was read by Range to get the 
 * proper sorting and validation-regex for the custom numbering. The edition 
 * creation routines would also want to read this config file to determine 
 * the number of pieces to create. Better still, creation routines would query 
 * this class for that information.
 * A config file would let us add new schemes at will without changing the 
 * code base. We could also use a user-prefs based system to allow user created 
 * special schemes. In that case we would want to route new user-created 
 * configurations to the system admin for review.
 * 
 * @todo Can we do a Series range? Or some other range-on-this-set?
 * One onerous task I've had to do is transfer same-number-piece from 
 * several Editions to a Disposition. It would be great to be able to name 
 * a set of Edition/Formats and a piece number (or numbers) then have all 
 * the pieces from those Editions chosen. The Series concept would represent 
 * a pre-defined Edition/Formats set. 
 * One implication of this idea is that there might end up being a separate 
 * class that gathers and returns the pieces and other necessary Entities. 
 * This class should probably focus on proper syntax (and reference validity?). 
 * 
 * @todo Expand this class to do validation against real data
 * The two issues discussed above suggest:
 * This class should receive Edition and or Format objects so the range strings 
 * can be validated against the real data; is it the right numbering symbols; 
 * do the pieces exist; do the editions or series members exist; etc.
 * Validation should start with a minimal object (Edition Entity?) so the 
 * numbering style and basic range could be used to check the basic syntax 
 * before getting into issues of piece availability
 * 
 * @author dondrake
 */
class Range {

    /**
     * The string describing the range
     * 
     * 1-7, 13, 20, 23-30
     *
     * @var string
     */
    static $range_string;

    /**
     * A numeric array with range as the values
     *
     * @var array
     */
    static $range_array;
    static $range_values;

    /**
     * An associative array
     * 
     * Range values are the keys.
     * 
     * @var array
     */
    static $range_assoc;
    static $range_keys;

    public function __get($param) {

        if (in_array($param, ['string', 'array', 'assoc', 'keys', 'values'])) {
            $name = "range_$param";
            return $this->$name;
        } else {
            throw new \BadMethodCallException("Range::__get() argument must be the string 'string', 'array' or 'assoc'.");
        }
    }

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
     * @todo the case of providing an array of arrays creates duplicate 
     *          values in the string [[2,3], [5,6], [5,7]], {n}.{n} 
     *          yields '2-3, 5, 5-7'. Assemble an array of values, shake 
     *          out duplicates, then process?
     * @todo make this work with arrays of objects? Extract properties 
     *          or get value by method call?
     * 
     * @param array $data
     * @param string $path
     * @return string
     */
    static function arrayToString($data = array(), $path = '') {
        self::$range_string = NULL;
        if (!is_array($data)) {
            $data = array();
        }
        $numbers = Hash::extract($data, $path);
        sort($numbers);

        foreach ($numbers as $index => $value) {
            $numbers[$index] = intval($value);
        }
        $list = new ArrayIterator($numbers);

        $range = $previous = FALSE;
        while ($list->valid()) {

            // if this is the first entry
            if (!self::$range_string) {
                self::$range_string = $previous = $list->current();
                $list->next();
                continue;
            }

            // if this is the next number in a sequence
            if ($list->current() === ($previous + 1)) {
                switch (self::$range_string[strlen(self::$range_string) - 1]) {
                    case '-':
                        break;
                    default:
                        self::$range_string .= '-';
                        break;
                }

                // if we jumped more than one number
            } else {
                switch (self::$range_string[strlen(self::$range_string) - 1]) {
                    case '-':
                        self::$range_string .= "$previous, {$list->current()}";
                        break;
                    default:
                        self::$range_string .= ", {$list->current()}";
                        break;
                }
            }

            $previous = $list->current();
            $list->next();
        }

        // check to see if we left a range unfinished
        if (self::$range_string[strlen(self::$range_string) - 1] === '-') {
            self::$range_string .= $previous;
        }

        return is_int(self::$range_string) ? (string) self::$range_string : self::$range_string;
    }

    /**
     * Turn a range string into an array with the sequence values
     * 
     * format: x-y, z, r, i-j
     * This will populate both array properties of the class
     * but will only return one based on the $type parameter.
     * Default: assoc
     * 
     * @param string $range
     * @param string $type 'assoc' or 'array' to get key=>values or values only
     * @return array
     */
    static function stringToArray($range, $type = 'array') {
//		var_dump(func_get_args());
//		var_dump($type);
//		echo 
//				die();

        if (!in_array($type, ['assoc', 'keys', 'array', 'values'])) {
            $msg = 'Range::stringToArray \'$type\' must be the string'
                . ' \'assoc\', \'keys\',  \'array\' or \'values\'';
            throw new \BadMethodCallException($msg);
        }

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
        sort($sequence);

        // filter out the duplicates
        self::$range_array = self::$range_values = array_flip(array_flip($sequence));
        self::$range_assoc = self::$range_keys = array_flip(self::$range_array);

        $type = 'range_' . $type;

        return self::$$type;
    }

    /**
     * Insure the range describing numbered pieces to move is valid 
     * 
     * @todo Stub from App\Form\AssignmentForm. That class uses this method 
     *          as a callable ('rule' => [$this, 'rangePatternValidation']). 
     *          That code will have to be changed to call this static version  
     * 
     * @param mixed $value
     * @param array $context
     * @return boolean
     */
    static function patternValidation($value) {
        $pattern = '/(\d+-\d+|\d+)(, *(\d+-\d+|\d+))*/';
        preg_match($pattern, $value, $match);
        
        return isset($match[0]) && ($value === $match[0]);
    }

}

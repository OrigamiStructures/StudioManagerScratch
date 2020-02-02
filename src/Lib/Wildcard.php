<?php
namespace App\Lib;

/**
 * Wildcard
 *
 * Append, prepend, or wrap a string in sql LIKE characters '%'
 * or other single character delimiter
 *
 * @author dondrake
 */
class Wildcard {

    /**
     * Add % wildcard character(s) to a string (or other delimiter char)
     *
     * @param string $string The string to modify
     * @param string $location start, before, after, end, both, or wrap
     * @param string $delimiter % or any other single character
     * @return string
     */
    static public function add($string, $location, $delimiter = '%') {
        $delimiter = self::clipDelimiter($delimiter);
        switch ($location) {
            case 'before':
            case 'start':
                $string = self::doBefore($string, $delimiter);
                break;
            case 'after':
            case 'end':
                $string = self::doAfter($string, $delimiter);
                break;
            case 'both':
            case 'wrap':
                $string = self::doWrap($string, $delimiter);
                break;
        }
        return $string;
    }

    /**
     * Add a delimiter to the beginning of a string
     *
     * But don't duplicate if it's already there
     *
     * @param string $string
     * @param string $delimiter
     */
    static public function before($string, $delimiter = '%') {
        $delimiter = self::clipDelimiter($delimiter);
        return self::doBefore($string, $delimiter);
    }

    /**
     * Add a delimiter to the end of a string
     *
     * But don't duplicate if it's already there
     *
     * @param string $string
     * @param string $delimiter
     */
    static public function after($string, $delimiter = '%') {
        $delimiter = self::clipDelimiter($delimiter);
        return self::doAfter($string, $delimiter);
    }

    /**
     * Add a delimiter to the both ends of a string
     *
     * But don't duplicate if it's already there
     *
     * @param string $string
     * @param string $delimiter
     */
    static public function wrap($string, $delimiter = '%') {
        $delimiter = self::clipDelimiter($delimiter);
        return self::doWrap($string, $delimiter);
    }

    /**
     * Enclose the string in the provided brackets
     *
     * The bracket string will be split in half, half to front, half to back
     *
     * @param $string
     * @param string $brackets
     * @return string
     */
    static public function bracket($string, $brackets = '[]') {
        list($start, $end) = explode(PHP_EOL, chunk_split($brackets, strlen($brackets) / 2));
        return trim($start) . trim($string) . trim($end);
    }

    static public function tag($string, $type) {
        return "<$type>" . trim($string) . "</$type>";
    }

    /**
     * Perform the pre-pending action
     *
     * @param string $string
     * @param string $delimiter
     */
    static private function doBefore($string, $delimiter) {
        if (strpos($string, $delimiter) !== 0) {
          $string = "$delimiter$string";
        }
        return $string;
    }

    /**
     * Perform the appending action
     *
     * @param string $string
     * @param string $delimiter
     */
    static private function doAfter($string, $delimiter) {
        // skip over any earlier $delimiter, only see the last
        if (strpos($string, $delimiter,strlen($string)-1) !== strlen($string)-1) {
          $string = "$string$delimiter";
        }
        return $string;
    }

    /**
     * Perform the wrapping action
     *
     * @param string $string
     * @param string $delimiter
     */
    static private function doWrap($string, $delimiter) {
        $string = trim($string, $delimiter);
        return "$delimiter$string$delimiter";
    }

    /**
     * Insure the delimiter is only one character
     *
     * @param string $delimiter
     * @return string
     */
    static private function clipDelimiter($delimiter) {
        if (strlen($delimiter) > 1) {
            $chars = str_split($delimiter);
            $delimiter = array_shift($chars);
        }
        return $delimiter;
    }
}

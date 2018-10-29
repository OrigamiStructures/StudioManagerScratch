<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Query;
use App\Lib\Range;

/**
 * IntegerQueryBehavior
 * 
 * Consolidate and simplify param requirements for searching INT fields
 * 
 * <code>
 * // parameter order not important
 * ['between', 5, 9];
 * ['<', 3]; // any equality/inequality operator
 * [4];
 * ['2-3, 5']; // range strings, see App\Lib\Range
 * [3, 5, '6', '24']
 * </code>
 * 
 * @todo Chained queries seem to overlay their parameter arrays. So if the first 
 *      in the chain has 3 elements and the second sets an array with 1 element, 
 *      the second with actually recieve 3 elements with the first overwritten.
 *      That's gonna be a problem with this 'deductive' class. 
 *      The query seems to do some cleanup, but it's based on associative keys 
 *      and possibly only named ones.
 *
 * @author dondrake
 */
class IntegerQueryBehavior extends Behavior{
    
    /**
     * Search for a value or range in an INT field
     * <pre>
     * ['between', 5, 9];
     * ['<', 3]; 
     * [4];
     * ['2-3, 5']; 
     * [3, 5, '6', '24']
     * </pre>
     * @param Query $query
     * @param string $column
     * @param array $params
     * @return Query
     */
    public function integer($query, $column, $params) {
        if (in_array('between', $params, TRUE)) {
            osd('between');
            osd($query->getOptions());
            return $this->constructBetween($query, $column, $params);
        }
        if ($op = array_intersect(['<', '>', '=', '<=', '>='], $params)) {
            osd('comparison');
            osd($query->getOptions());

            return $this->constructComparison($query, $column, $op, $params);
        }
        if (count($params) > 1) {
            osd($params, 'in');
            osd($query->getOptions());
            return $query->where(["$column IN" => $params]);
        }
        $keys = array_keys($params);
        $key = array_shift($keys);
        $value = $params[$key];
        if ($value === intval($value)) {
//            osd($params, 'single value ' . $value);
            return $query->where([$column => $value]);
        }
        if (Range::patternValidation($value)) {
            $values = Range::stringToArray($value);
//            osd($values, 'the array from ' . $value);
            return $query->where(["$column IN" => $values]);      
        }
        return $query;
        
    }
    
    private function constructComparison($query, $column, $op, $params) {
        $value = array_diff($params, ['between']);
        if (count($value) > 0){
            return $query->where(["$column $op" => array_shift($value)]);
        } else {
            $msg = "'between' requires two integers, none given.";
            throw new BadMethodCallException($msg);
       }
    }
    
    private function constructBetween($query, $column, $params) {
        $values = array_diff($params, ['between']);
        if (count($values) > 1){
            sort($values);
            return $query->where(function ($exp, $q)use ($values, $column) {
                return $exp->between($column, array_shift($values), array_shift($values));
            });
        } else {
            $msg = "'between' requires two integers, " . count($values) . ' given.';
            throw new \BadMethodCallException($msg);
       }
            
    }
}

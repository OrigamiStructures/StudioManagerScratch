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
 * ['<', 3]; // any comparison operator
 * [4];
 * ['2-3, 5']; // range strings, see App\Lib\Range
 * [3, 5, '6', '24']
 * // or any simple exposed value or range string
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
     * ['<', 3]; // any comparison operator
     * [13];
     * ['2-3, 5']; 
     * [3, 5, '6', '24']
     * </pre>
     * @param Query $query
     * @param string $column
     * @param array $params
     * @return Query unaltered if an invalid arg param is sent
     */
    public function integer(Query $query, $column, $params) {
        
        if (in_array('between', $params)) {
            return $this->constructBetween($query, $column, $params);
        }
        
        $op = array_intersect(['<', '>', '=', '<=', '>='], $params);
        if ($op) {
            return $this->constructComparison($query, $column, $op, $params);
        }
        
        if (count($params) > 1) {
            return $query->where(["$column IN" => $params]);
        }
        
        $value = (string) array_shift($params);
        if (!empty($value)) {
            return $this->constructFromRange($query, $column, $value);
        }
        
        return $query;
    }
    
    private function constructComparison($query, $column, $op, $params) {
        $value = array_diff($params, $op);
        $op = array_shift($op);
        if (count($value) > 0){
            return $query->where(["$column $op" => array_shift($value)]);
        } else {
            $msg = "'$op' comparison requires an integer to compare to, none given.";
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
    
    private function constructFromRange($query, $column, $range) {
        if(!Range::patternValidation($range)){
            return $query;
        }
        $values = Range::stringToArray($range);
        if (count($values) === 1) {
            return $query->where([$column => $values[0]]);
        } else {
            return $query->where(["$column IN" => $values]);
        }
    }
}

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
            return $this->constructBetween($query, $column, $params);
        }
        if ($op = array_intersect(['<', '>', '=', '<=', '>='], $params)) {
            return $this->constructComparison($query, $column, $op, $params);
        }
        $value = array_shift($params);
        if ($value == intval($value)) {
            return $query->where([$column, $value]);
        }
        if ($values = Range::patternValidation($value)) {
            return $query->where(["$column IN", $values]);      
        }
        // finally we'll assume it's an array of values
        return $query->where(["$column IN", $params]);
    }
    
    private function constructComparison($query, $column, $op, $params) {
        $value = array_diff($params, ['between']);
        if (count($value) > 0){
            return $query->where(["$column $op", array_shift($value)]);
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

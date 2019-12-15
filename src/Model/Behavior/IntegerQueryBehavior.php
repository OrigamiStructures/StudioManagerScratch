<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Query;
use App\Lib\Range;

/**
 * IntegerQueryBehavior
 * 
 * Deduce the correct INT query based on the $params structure
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
     * 
     * @param Query $query
     * @param string $column Name of the column to search
     * @param array $params Any of the variations described above
     * @return Query unaltered if an unrecognized $params structure is sent
     */
    public function integer(Query $query, $column, $params) {
        
        if (count($params) == 0) {
            $query = $query->where('6 = 9');
        }

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
    
    /**
     * Construct a 'where' using a provided comparison operator
     * 
     * @param Query $query
     * @param string $column
     * @param string $op
     * @param array $params
     * @return Query
     * @throws BadMethodCallException
     */
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
    
    /**
     * Construct a 'between' statement
     * 
     * @param Query $query
     * @param string $column
     * @param array $params
     * @return Query
     * @throws \BadMethodCallException
     */
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
    
    /**
     * Construct a 'where =' or 'in ( )' statement
     * 
     * @param Query $query
     * @param string $column
     * @param string $range A valid App\Lib\Range string
     * @return Query
     */
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

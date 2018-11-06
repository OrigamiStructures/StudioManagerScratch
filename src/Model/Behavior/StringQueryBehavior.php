<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * StringQueryBehavior
 * 
 * Deduce the correct string query to produce
 *
 * @author dondrake
 */
class StringQueryBehavior extends Behavior{
    
    /**
     * Simplified text field search
     * 
     * If the param $string has a leading or trailing '%' character, 
     * a LIKE query will be used. Otherwise, equality will be evaluated. 
     * 
     * @param Query $query
     * @param string $column
     * @param string $options
     * @return $query
     */
    public function string(Query $query, $column, $string) {
        if (is_array($string)) {
            $string = array_shift($string);
        }
        // we will allow embedded % but leading/trailing will cause LIKE
        if(strlen($string) > strlen(trim($string, '%'))) {
            return $this->constructLikeQuery($query, $column, $string);
        }
        return $this->constructMatchQuery($query, $column, $string);
    }
    
    /**
     * Make a LIKE clause for the query
     * 
     * @param Query $query
     * @param string $column
     * @param string $needle
     * @return Query
     */
    private function constructLikeQuery($query, $column, $needle) {
        return $query->where(["$column LIKE" => $needle]);
    }
    
    /**
     * Make an equality check for the query
     * 
     * @param Query $query
     * @param string $column
     * @param string $needle
     * @return Query
     */
    private function constructMatchQuery($query, $column, $needle) {
        return $query->where([$column => $needle]);
    }
}

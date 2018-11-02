<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;

/**
 * StringQueryBehavior
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
        // we will allow embedded % but leading/trailing will cause LIKE
        if(strlen($string) > strlen(trim($string, '%'))) {
            return $this->_stringLike($query, $column, $string);
        }
        return $this->_stringMatch($query, $column, $string);
    }
    
    protected function _stringLike($query, $column, $needle) {
        return $query->where(["$column LIKE" => $needle]);
    }
    
    protected function _stringMatch($query, $column, $needle) {
        return $query->where([$column => $needle]);
    }
}

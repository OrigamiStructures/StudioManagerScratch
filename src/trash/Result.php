<?php
namespace App\Model\Entity\Traits;

use Cake\Cache\Cache;

/**
 * Result
 * 
 * Result is a utility class that takes in an array of entities of a single 
 * type, then looks up supporting data that will be required for rendering. 
 * 
 * The initial use case is looking up Artwork stacks to support found sets of 
 * downstream records (like Pieces or Dispositions). The stacks (and other 
 * kinds of supporting data) are assumed to be complex, costly to query, and 
 * worth caching.
 * 
 * The entities could be configured with information about what cache and  
 * support-data discovery method to use as well as methods to return necessary 
 * values like the link value or the name of the property that holds it.
 * 
 * Or, rather than chasing down all the individual entity classes and 
 * setting them up (or even composing a trait into them) I could follow the 
 * pattern of the Cache system. I could make one master config file that 
 * holds all the set-ups, each named for the entity it applies to. 
 * 
 * The required configuration is spun up only when needed.
 * 
 * In fact, this could be written as a CacheEngine class. It would get an 
 * expanded set of config values to support the additional responsibilities 
 * of querying the support data if the cache doesn't exist and requirement 
 * that it should return data supporting many entities.
 *
 * @author Don Drake
 */
class Result {
    
    protected $_stack;
    
    protected $_wrapper;
    
    protected $_cacheConfig;

    protected $count = 0;

    public function counter() {
        return $this->count++;
    }
    
    public function process($entities) {
        foreach($entities as $entity) {
            $this->stack[$entity->stackLink()] =
                    Cache::remember($entity->stackKey(), [$class, 'method'], $entity->stackConfig());
        }
    }
    
    public function getStack($id = NULL) {
        return (is_null($id)) ? $this->stack : $this->stack[$id];
    }
}

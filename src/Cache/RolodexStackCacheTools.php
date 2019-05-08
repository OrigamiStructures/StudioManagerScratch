<?php
namespace App\Cache;

use Cake\Cache\Cache;
/**
 * RolodexStackCacheTools
 * 
 * Centralize key generation and config selection for RolodexStack caching.
 * Provide static cache-delete capabilities to simplify management 
 * during create, update, and delete processes.
 * 
 * The static functions are still accessible in the normal way when 
 * the class is instantiated (as it is in RolodexStacks)
 *
 * @author dondrake
 */
class RolodexStackCacheTools {
	
	/**
	 * Generate an RolodexStack cacke key
	 * 
	 * @param string $key An Rolodexwork id
	 * @return string The key
	 */
	public static function key($key) {
		return $key;
	}
	
	/**
	 * Get the Cache config for the RolodexStacks
	 * 
	 * @return string
	 */
	public static function config() {
		return 'rolodexstack';
	}
	
	/**
	 * Delete a set of RolodexStacks
	 * 
	 * @param array $ids rolodexwork IDs
	 */
	public static function delete(array $ids) {
		foreach ($ids as $id) {
			Cache::delete(self::key($id), self::config());
		}
	}
	
}

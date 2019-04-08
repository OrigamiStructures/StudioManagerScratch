<?php
namespace App\Cache;

use Cake\Cache\Cache;
/**
 * ArtStackCacheTools
 * 
 * Centralize key generation and config selection for ArtStack caching.
 * Provide static cache-delete capabilities to simplify management 
 * during create, update, and delete processes.
 * 
 * The static functions are still accessible in the normal way when 
 * the class is instantiated (as it is in ArtStacks)
 *
 * @author dondrake
 */
class ArtStackCacheTools {
	
	/**
	 * Generate an ArtStack cacke key
	 * 
	 * @param string $key An Artwork id
	 * @return string The key
	 */
	public static function key($key) {
		return $key;
	}
	
	/**
	 * Get the Cache config for the ArtStacks
	 * 
	 * @return string
	 */
	public static function config() {
		return 'artstack';
	}
	
	/**
	 * Delete a set of ArtStacks
	 * 
	 * @param array $ids artwork IDs
	 */
	public static function delete(array $ids) {
		foreach ($ids as $id) {
			Cache::delete(self::key($id), self::config());
		}
	}
	
}

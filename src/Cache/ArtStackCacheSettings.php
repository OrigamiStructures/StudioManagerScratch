<?php
namespace App\Cache;
/**
 * Description of ArtStackCacheSettings
 *
 * @author dondrake
 */
class ArtStackCacheSettings {
	
	public function key($key) {
		return $key;
	}
	
	public function config() {
		return 'artstack';
	}
}

<?php
namespace App\Lib\Traits;

use Cake\Cache\Cache;

/**
 * Description of EditionStackCache
 *
 * @author dondrake
 */
trait EditionStackCache {

	protected $configuration = 'editionStack';

	/**
	 * Delete an edition type stackQuery
	 *
	 * @param string $id
	 */
	public function clearCache($id) {
		return Cache::delete($this->cache_key($id), $this->configuration);
	}

	/**
	 * Read an edition type stackQuery
	 *
	 * @param string $id
	 */
	public function readCache($id) {
		return Cache::read($this->cache_key($id), $this->configuration);
	}

	/**
	 * Write an edition type stackQuery
	 *
	 * @param string $id
	 * @param mixed $data
	 */
	public function writeCache($id, $data) {
		return Cache::write($this->cache_key($id), $data, $this->configuration);
	}

	/**
	 * Create the edition type stackQuery cache key
	 *
	 * @param string $id
	 * @return string
	 */
	private function cache_key($id) {
		return "{$this->contextUser()->artistId()}_$id";
	}
}

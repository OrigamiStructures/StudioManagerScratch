<?php
namespace App\Lib\Traits;

use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;


/**
 * ArtReviewTrait handles logic common to viewing Artwork, Editions, and Formats
 * 
 * Detecting 'flat' Artwork, that which has a single Edition or a 
 * single Edition with a single Format, is part of an effort to aid 
 * the user's swift navigation to the most detailed level of display. 
 * To avoid time lost to redundant code  as the system redirects the 
 * request to the proper display context, this trait was created.
 * 
 * @author dondrake
 */
trait ArtReviewTrait {
	
	/**
	 * Is the Artwork single-edition or single-edition + single-format
	 * 
	 * Depending on the flatness and the controller that was called we 
	 * may want to redirect to a different controller to give the 
	 * most detailed view (without requiring navigation)
	 */
	protected function _try_flatness_redirect($artwork_id, $edition_id = FALSE) {
		$s = microtime();
		$redirect_needed = Cache::read($this->_cache_name($artwork_id, $edition_id), 'flatness');
		if ($redirect_needed === FALSE) {
			$redirect_needed = $this->_flatness_determination($artwork_id, $edition_id);
			Cache::write($this->_cache_name($artwork_id, $edition_id), $redirect_needed, 'flatness');
		}
		if (is_null($redirect_needed)) {
			return;
		} elseif (is_array($redirect_needed)) {
			extract($redirect_needed); //$controller, $query_args[] (1-3 args = layer IDs)
			$this->redirect([
				'controller' => $controller, 
				'action' => 'review', 
				'?' => $query_args]
			);
		}
	}
	
	/**
	 * Return the cache key that holds calculated 'flatness' information
	 * 
	 * @param string $artwork_id
	 * @return string
	 */
	private function _cache_name($artwork_id, $edition_id) {
		return "_{$this->SystemState->artistId()}_$artwork_id" . 
			$edition_id ? "_$edition_id" : '';
	}
	
	/**
	 * Decide if Artwork is flat and if so, how deep flatness goes
	 * 
	 * We may enter from Artwork->review() or Edition->review with 
	 * the ID know down to the level we call from. The goal is to 
	 * find if there is only a single child (or single child/grandchild) 
	 * and if so, redirect to display from that lowest-singleton controller. 
	 * This will show the record in its most detailed form without 
	 * requiring the user to navigate to that detailed condition.
	 * 
	 * @param string $artwork_id
	 * @param string|FALSE $edition_id
	 * @return boolean|array FALSE (no redirect) or url values for redirect
	 */
	private function _flatness_determination($artwork_id, $edition_id) {
		$redirect_needed = NULL;
		$conditions = [
			'user_id' => $this->SystemState->artistId(),
		];
		if (!$edition_id) {
			/*
			 * If only $artwork_id is provided (Artwork controller context) 
			 * we need to see if Edition is flat, and if so, prepare 
			 * the necessary redirect data
			 */
			$this->Editions = TableRegistry::get('Editions');
			$query = $this->Editions->find()
						->where($conditions + ['artwork_id' => $artwork_id]);
			$total_editions = $query->count();
			if ($total_editions === 1) {
				$edition = $query->first();
				$edition_id = $edition->id;
				$redirect_needed = [
					'controller' => 'editions',
					'query_args' => [
						'artwork' => $artwork_id,
						'edition' => $edition_id,
					],
				];
			} 
		}
		/*
		 * At this point there are three possible conditions:
		 * 
		 * 1 - $edition_id is known and we were called from Edition context, 
		 *		so we need to check Format flatness
		 * 2 - we were called Artwork context and found that the Edition was 
		 *		flat and we set $edition_id. So we need to check Format flatness. 
		 * 3 - $edition_id is not known. This means we came from Artwork context 
		 *		and no redirect is required.
		 * 
		 * In case #2, $redirect_needed has become the redirect data array
		 */
		if ($edition_id) {
			$this->Formats = TableRegistry::get('Formats');
			$query = $this->Formats->find()
						->where($conditions + ['edition_id' => $edition_id]);
			$total_formats = $query->count();
			if ($total_formats === 1) {
				$format = $query->first();
				$redirect_needed = [
					'controller' => 'formats',
					'query_args' => [
						'artwork' => $artwork_id,
						'edition' => $edition_id,
						'format' => $format->id,
					],
				];
			}
		}
		/*
		 * Now there are two possible conditions:
		 * 
		 * 1 - $redirect_needed = NULL, no redirect needed
		 * 2 - $redirect_needed = an array of data identifying the redirect 
		 */
		return $redirect_needed;
	}
}

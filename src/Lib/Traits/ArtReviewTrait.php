<?php
namespace App\Lib\Traits;

use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\NotImplementedException;
//use App\View\Helper\ArtStackElementHelper;
//use App\View\Helper\DispositionToolsHelper;


/**
 * ArtReviewTrait handles logic common to viewing Artwork, Editions, and Formats
 * 
 * Detecting 'flat' Artwork, that which has a single Edition or a 
 * single Edition with a single Format, is part of an effort to aid 
 * the user's swift navigation to the most detailed level of display. 
 * To avoid time lost to redundant code  as the system redirects the 
 * request to the proper display context, this trait was created.
 * 
 * 06/18 This is pending further analysis:
 *		Added Helper loading features to reduce coupling of Artwork 
 * create/review/refine Templates and Elements. In the original 
 * incarnation, the views all self-constructed beginning from Artworks/review. 
 * Logic in each template or element decided the variables to pass on, the 
 * Helpers to load, and the next elements to render. This made editing difficult 
 * because there was no way (short of expert knowledge) to tell if a change 
 * would effect some downstream process.
 *		The reason for this strategy was to insure all Artwork rendering followed a 
 * common DOM pattern (to simplify css and ajax calls). While I did accomplish 
 * that goal, it created a coupling problem. So, I'm going to back the common  
 * Helper loading into this Trait and other more specific loading into actions.
 *		Part of this is accomplished by having a Artworks, Editions, and Formats 
 * Controllers extend a new Parent class which loads a new parent View class for 
 * rendering.
 * 
 * @author dondrake
 */
trait ArtReviewTrait {
	
	/**
	 * Is the Artwork single-edition or single-edition + single-format
	 * 
	 * Depending on the flatness and the controller that called we 
	 * may want to redirect to a different controller to give the 
	 * most detailed view (without requiring navigation)
	 */
	protected function _try_flatness_redirect($artwork_id, $edition_id = FALSE) {
		$redirect_needed = Cache::read($this->_cache_name($artwork_id, $edition_id), 'flatness');
		if ($redirect_needed === FALSE) {
			$redirect_needed = $this->_flatness_determination($artwork_id, $edition_id);
			Cache::write($this->_cache_name($artwork_id, $edition_id), $redirect_needed, 'flatness');
			if (!$edition_id && $redirect_needed['controller'] === 'editions') {
				/*
				 * If the call was from ArtworksController and flatness was found 
				 * at Edition level, the redirect will call this process again 
				 * but we already know Edition is flat. So we can write the cache 
				 * to record that knowledge.
				 */
				Cache::write($this->
					_cache_name($artwork_id, $redirect_needed['query_args']['edition']), 
					NULL, 'flatness');
			}
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
		} else {
			throw new NotImplementedException(
				'Artwork flatness determination must return NULL or an array. A '. 
				gettype($redirect_needed) . ' was received.');
		}
	}
	
	/**
	 * Return the cache key that holds calculated 'flatness' information
	 * 
	 * @param string $artwork_id
	 * @param FALSE|string $edition_id
	 * @return string
	 */
	private function _cache_name($artwork_id, $edition_id) {
		return "_{$this->SystemState->artistId()}_$artwork_id" . 
			($edition_id ? "_$edition_id" : '');
	}
	
	/**
	 * Clear a single flatness cache entry
	 * 
	 * @param string $artwork_id
	 * @param FALSE|string $edition_id
	 * @return boolean
	 */
	protected function _drop_flatness_cache($artwork_id, $edition_id = FALSE) {
		return Cache::delete($this->_cache_name($artwork_id, $edition_id), 'flatness');
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
	 * @return null|array NULL (no redirect) or url values for redirect
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

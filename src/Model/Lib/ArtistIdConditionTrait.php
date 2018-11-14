<?php
namespace App\Model\Lib;
/**
 * ArtistIdConditionTrait
 * 
 * A method to insure the artist_id is included in the query. This 
 * will prevent bleed through of one user's data into another even if 
 * some crazy hack or error calls for record IDs that would otherwise 
 * cause the records to be exposed.Since IDs are exposed in the URL, this is critical.
 * 
 * Since IDs are exposed in the URL, this is critical. 
 * 
 * Suggested use; call from inside the Tables beforeFind() event. 
 * <code>
 * 	public function beforeFind($event, $query, $options, $primary) { 
 *		$this->includeArtistIdCondition($query);
 *	}
 * </code>
 * 
 * Also includes a property to insure the process is only done once.
 * 
 * @author dondrake
 */
trait ArtistIdConditionTrait {
	
	/**
	 * Track whether the artist_id has been added to the query where clause
	 * 
	 * Avoid adding the statement multiple times.  
	 * This will be an SplObjectStorage object. Testing with ->contains($query) 
	 * yields a boolean to indicate done (TRUE), not done (FALSE).
	 */
	protected $_where_artist_id = FALSE;

	/**
	 * Insure the query conditions include artist_id(s) checks
	 * 
	 * Only do this once for each query object
	 * 
	 * @param Query $query
	 */
	protected function includeArtistIdCondition($query) {
            
            // ignore for command line fixture baking
            if (php_sapi_name() === 'cli' && Configure::read('debug')) { return; } 
            
            // First time through initialize the property
            if (!$this->_where_artist_id) {
                    $this->_where_artist_id = new \SplObjectStorage();
            }
            if (!$this->_where_artist_id->contains($query)) {
                    $this->_where_artist_id->attach($query, TRUE);
                    $this->addArtistIdCondition($query, $this->SystemState->artistId());
            }
	}
	
	/**
	 * Actually add the where statement for user_id = artist_id
	 * 
	 * @param Query $query
	 * @param int|array $id Artist managers might have an array of IDs
	 */
	protected function addArtistIdCondition($query, $id) {
		if(is_array($id)) {
			$query->where(["$this->_alias.user_id IN" => $id]);
		} else {
			$query->where(["$this->_alias.user_id" => $id]);
		}
	}
}

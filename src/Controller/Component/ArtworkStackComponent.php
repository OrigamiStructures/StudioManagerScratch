<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use App\Model\Table\EditionsTable;
use App\Model\Table\FormatsTable;
use App\Model\Table\PiecesTable;
use App\Model\Table\SubscriptionsTable;
use App\Model\Table\SeriesTable;
use Cake\ORM\TableRegistry;

/**
 * CakePHP ArtworkStackComponent
 * @author dondrake
 */
class ArtworkStackComponent extends Component {
	
	public $SystemState;
		
    public function initialize(array $config) 
	{
		$this->controller = $this->_registry->getController();
		$this->SystemState = $this->controller->SystemState;
		$this->Artworks = TableRegistry::get('Artworks', ['SystemState' => $this->SystemState]);
		
		$this->Editions = TableRegistry::get('Editions');
		$this->Formats = TableRegistry::get('Formats', ['SystemState' => $this->SystemState]);
		$this->Pieces = TableRegistry::get('Pieces', ['SystemState' => $this->SystemState]);
		$this->Series = TableRegistry::get('Series', ['SystemState' => $this->SystemState]);
		$this->Subscriptions = TableRegistry::get('Subscriptions', ['SystemState' => $this->SystemState]);
	}
	
	/**
	 * Prepare appropriate choice lists for all artwork stack tables
	 * 
	 * NEEDS TO BE 'STATE' AWARE TO GENERATE PROPERLY FILTERED AND 
	 * CONSTRUCTED LISTS FOR 'CREATION' VS 'SELECTION' PROCESSES
	 * 
	 * @return array
	 */
	public function layerChoiceLists() {
		//
		//
		// THESE RESULTS NEED TO BE CACHED TO CUT DOWN ON OVERHEAD
		//
		//
		$artist_id = $this->SystemState->artistId();
		$artworks = $this->Artworks->find('choiceList', ['artist_id' => $artist_id])->toArray();
		
		// UNIQUE EDITIONS CAN'T GET NEW FORMATS... MODIFY THE QUERY? ALWAYS?
		// ALSO FILTER OUT FULLY COMMITED EDTIONS (NO CANDIDATE PIECES)
		$editions = $this->Editions->find('choiceList', ['artist_id' => $artist_id])->toArray();
		
		$formats = $this->Formats->find('choiceList', ['artist_id' => $artist_id])->toArray();
		
		// ONLY THE SERIES NOT ALREADY IMPLEMENTED BY THE ARTWORK
		$series = $this->Series->find('choiceList', ['artist_id' => $artist_id])->toArray();
		$series = ['n' => 'New Series'] + $series;
		$subscriptions = $this->Subscriptions->find('choiceList', ['artist_id' => $artist_id])->toArray();
		
		$this->controller->set(compact('artworks', 'editions', 'formats', 'series', 'subscriptions'));
		return [$artworks, $editions, $formats, $series, $subscriptions];
	}
}

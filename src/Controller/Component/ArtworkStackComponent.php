<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use App\Model\Table\EditionsTable;
use App\Model\Table\FormatsTable;
use App\Model\Table\PiecesTable;
use App\Model\Table\SubscriptionsTable;
use App\Model\Table\SeriesTable;
use Cake\ORM\TableRegistry;
use Cake\Collection\Collection;

/**
 * CakePHP ArtworkStackComponent
 * @author dondrake
 */
class ArtworkStackComponent extends Component {
	
	public $SystemState;
	
	private $required_tables = [
		'Artworks', 'Editions', 'Formats', 'Pieces', 'Series', 'Subscriptions'
	];
		
    public function initialize(array $config) 
	{
		$this->controller = $this->_registry->getController();
		$this->SystemState = $this->controller->SystemState;
		
//		$mili = microtime();
		$tables = new Collection($this->required_tables);
		$tables->each(function($alias, $index) {
			if (TableRegistry::exists($alias)) {
				$this->$alias = TableRegistry::get($alias);
			} else {
				$this->$alias = TableRegistry::get($alias, ['SystemState' => $this->SystemState]);
			}
		});
//		osd(microtime() - $mili, 'make models');
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

		$artworks = $editions = $formats = $series = $subscriptions = [];
		if (6 == 9) {
			//
			//
		// THESE RESULTS NEED TO BE CACHED TO CUT DOWN ON OVERHEAD
			//
		//
//		$mili = time()+  microtime();
			$artist_id = $this->SystemState->artistId();
			$artworks = $this->Artworks->find('choiceList', ['artist_id' => $artist_id])->toArray();


			// UNIQUE EDITIONS CAN'T GET NEW FORMATS... MODIFY THE QUERY? ALWAYS?
			// ALSO FILTER OUT FULLY COMMITED EDTIONS (NO CANDIDATE PIECES)
			$editions = $this->Editions->find('choiceList', ['artist_id' => $artist_id])->toArray();
			$types = $this->Editions->typeList();


			$formats = $this->Formats->find('choiceList', ['artist_id' => $artist_id])->toArray();


			$series = $this->Series->choiceList(['artist_id' => $artist_id])->toArray();
			$series = ['n' => 'New Series'] + $series;
			$subscriptions = $this->Subscriptions->find('choiceList',
							['artist_id' => $artist_id])->toArray();
		}		
		$this->controller->set(compact('artworks', 'editions', 'types', 'formats', 'series', 'subscriptions'));
//		osd((time()+  microtime()) - $mili, 'do queries');
		return [$artworks, $editions, $formats, $series, $subscriptions];
	}
	
	public function testme($id, $index_name) {
		return $this->Editions->choiceList($id, 'artwork');

	}
}

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
//use Cake\Controller\Component\PaginatorComponent;

/**
 * CakePHP ArtworkStackComponent
 * @author dondrake
 */
class ArtworkStackComponent extends Component {
	
	public $components = ['Paginator'];


	public $SystemState;
	
	/**
	 * Storage of the current page of Artworks if it's been fetched
	 *
	 * @var ResultSet
	 */
	protected $artworksPage = FALSE;
	
	/**
	 *
	 * @var Entity
	 */
	protected $knownArtwork = FALSE;
	protected $key = NULL;

	/**
	 * Get a named Table instance
	 * 
	 * Lazy load Tables in the Artwork Stack
	 * 
	 * @param string $name
	 * @return Table
	 */
	public function __get($name) {
		parent::__get($name);
		// 
		if (!empty($this->$name)) {
			return $this->$name;
		} else if (in_array($name, $this->required_tables)) {
			if (TableRegistry::exists($name)) {
				$this->$name = TableRegistry::get($name);
			} else {
				$this->$name = TableRegistry::get($name, ['SystemState' => $this->SystemState]);
			}
			return $this->$name;
		}
	}


	private $required_tables = [
		'Artworks', 'Editions', 'Formats', 'Pieces', 'Series', 'Subscriptions', 'Menus'
	];
		
    public function initialize(array $config) 
	{
		$this->controller = $this->_registry->getController();
		$this->SystemState = $this->controller->SystemState;
	}
	
	/**
	 * Call from anywhere in the ArtworkStack to get the proper result
	 * 
	 * @return Entity
	 */
	public function stackQuery() {
		if (!$this->SystemState->isKnown('artwork')) {
			return $this->Paginator->paginate($this->Artworks, [
				'contain' => ['Users', 'Images', 'Editions' => ['Formats']]
			]);
		} else {
			$this->key('artwork', $this->SystemState->queryArg('artwork'));
			return $this->Artworks->get($this->key('artwork'), [
				'contain' => ['Users', 'Images', 'Editions' => ['Formats' => ['Images']]]
			]);
		}
	}
	
	public function key($name = NULL, $value = NULL) {
		if (!is_null($value)) {
			$this->key[$name] = $value;
		} else if (!is_null($name)) {
			return isset($this->key[$name]) ? $this->key[$name] : NULL;
		} else {
			return $this->key;
		}
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

//		$artworks = $editions = $formats = $series = $subscriptions = [];
		if (6 == 6) {
			//
			//
		// THESE RESULTS NEED TO BE CACHED TO CUT DOWN ON OVERHEAD
			//
		//
//		$mili = time()+  microtime();
			$artist_id = $this->SystemState->artistId();
//			$artworks = $this->Artworks->find('choiceList', ['artist_id' => $artist_id])->toArray();


			// UNIQUE EDITIONS CAN'T GET NEW FORMATS... MODIFY THE QUERY? ALWAYS?
			// ALSO FILTER OUT FULLY COMMITED EDTIONS (NO CANDIDATE PIECES)
//			$editions = $this->Editions->find('choiceList', ['artist_id' => $artist_id])->toArray();
			$types = $this->Editions->typeList();
//			osd($types);


			$formats = $this->Formats->find('choiceList', ['artist_id' => $artist_id])->toArray();


			$series = $this->Series->choiceList(['artist_id' => $artist_id])->toArray();
			$series = ['n' => 'New Series'] + $series;
			$subscriptions = $this->Subscriptions->find('choiceList',
							['artist_id' => $artist_id])->toArray();
		}		
//		$this->controller->set(compact('artworks', 'editions', 'types', 'formats', 'series', 'subscriptions'));
		$this->controller->set(compact('types', 'formats', 'series', 'subscriptions'));
//		osd((time()+  microtime()) - $mili, 'do queries');
//		return [$artworks, $editions, $formats, $series, $subscriptions];
		return [$formats, $series, $subscriptions];
	}
	
	public function testme($id, $index_name) {
		return $this->Editions->choiceList($id, 'artwork');

	}
}

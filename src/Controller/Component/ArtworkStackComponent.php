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
	
	protected $full_containment = [
		'Users', 'Images', 'Editions' => [
			'Series', 'Pieces', 'Formats' => [
				'Images', 'Pieces', 'Subscriptions'
				]
			]
		];

	private $required_tables = [
		'Artworks', 'Editions', 'Formats', 'Pieces', 'Series', 'Subscriptions', 'Menus'
	];
		
    public function initialize(array $config) 
	{
		$this->controller = $this->_registry->getController();
		$this->SystemState = $this->controller->SystemState;
	}

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
			/**
			 * This entire if statement was an earlier attempt to get SystemState 
			 * passed. It failed and so the hack described was added.
			 */
			if (TableRegistry::exists($name)) {
				$this->$name = TableRegistry::get($name);
				/**
				 * This line is a hack to resolve the problem that I couldn't find a 
				 * way to get this property to automatically travel into the tables 
				 * when a {Table}->get(id) call is made (which this->stackQuery() does). 
				 * Setting the property in AppTable construct, overriding the 
				 * Associtiation build calls in AppTable, addin the property to the 
				 * Associtiation definitions in the the various tables, having the 
				 * Controller use a different locateTable class that passes the value... 
				 * NONE of these techniques worked. I didn't try a {Table}->find(). 
				 * And I didn't look at any strategy that would worm the property down 
				 * into a behavior (because all the tables don't share a behavior).
				 */
				$this->$name->SystemState = $this->controller->SystemState; // HACK
			} else {
				$this->$name = TableRegistry::get($name, ['SystemState' => $this->controller->SystemState]);
			}
			return $this->$name;
		}
	}
	
	/**
	 * Call from anywhere in the ArtworkStack to get the proper result
	 * 
	 * @return Entity
	 */
	public function stackQuery() {
		// no 'artwork' query value indicates a paginated page
		if (!$this->SystemState->isKnown('artwork')) {
			$artworks = $this->Paginator->paginate($this->Artworks, [
				'contain' => $this->full_containment
			]);
			// menus need an untouched copy of the query for nav construction
			$this->controller->set('menu_artworks', clone $artworks);
			return $artworks;
		} else {
			// There may be more keys known than just the 'artwork', but that's 
			// all we need for the query.
			$artwork = $this->Artworks->get($this->SystemState->queryArg('artwork'), [
				'contain' => $this->full_containment
			]);
			// menus need an untouched copy of the query for nav construction
			$this->controller->set('menu_artwork', unserialize(serialize($artwork)));
			// create requires some levels to be empty so the forms don't populate
			if ($this->SystemState->is(ARTWORK_CREATE)) {
				return $this->pruneEntities($artwork);
			}
			return $artwork;
		}
	}
	
	/**
	 * Prepare the entity for 'creation' of some layer
	 * 
	 * stackQuery pulls the whole, known artwork stack and passes it here if 
	 * there is a Create request. In this case, we must insert empty Entities 
	 * on the appropriate layers. These are deduced by Controller context. 
	 * 
	 * @param Entity $artwork
	 * @return Entity
	 */
	protected function pruneEntities($artwork) {
		$controller = strtolower($this->SystemState->request->controller);
		/**
		 * This code got more complicated than I expected. So this clumsy 
		 * solution could be reviewed.
		 */
		if ($controller == 'editions') {
			$entity_class = get_class($artwork->{$controller}[0]);
			$artwork->$controller = [new $entity_class()];
		} else {
			$entity_class = get_class($artwork->editions[0]->{$controller}[0]);
			foreach ($artwork->editions as $index => $format) {
				$artwork->editions[$index]->$controller = [new $entity_class()];
			}
		}
		return $artwork;
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
	
}

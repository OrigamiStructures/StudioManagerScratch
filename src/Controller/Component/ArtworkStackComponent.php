<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use App\Model\Table\EditionsTable;
use App\Model\Table\FormatsTable;
use App\Model\Table\PiecesTable;
use App\Model\Table\SubscriptionsTable;
use App\Model\Table\SeriesTable;
use Cake\Collection\Collection;
use Cake\ORM\TableRegistry;
//use Cake\Controller\Component\PaginatorComponent;

/**
 * CakePHP ArtworkStackComponent
 * @author dondrake
 */
class ArtworkStackComponent extends Component {
	
	public $components = ['Paginator'];

	public $SystemState;
	
	public $full_containment = [
		'Users', 'Images', 'Editions.Users', 'Editions' => [
			'Series', 'Pieces', 'Formats.Users', 'Formats' => [
				'Images', 'Pieces', /*'Subscriptions'*/
				]
			]
		];

	private $required_tables = [
		'Artworks', 'Editions', 'Images', 'Formats', 'Pieces', 'Series', 'Subscriptions', 'Menus'
	];
		
    public function initialize(array $config) 
	{
		$this->controller = $this->_registry->getController();
		$this->SystemState = $this->controller->SystemState;
	}

//    public function implementedEvents()
//    {
//        $eventMap = [
//            'Controller.initialize' => 'beforeFilter',
//            'Controller.startup' => 'startup',
//            'Controller.beforeRender' => 'beforeRender',
//            'Controller.beforeRedirect' => 'beforeRedirect',
//            'Controller.shutdown' => 'shutdown',
//			'Model.buildRules' => 'buildRules',
//			];
//        $events = [];
//        foreach ($eventMap as $event => $method) {
//            if (method_exists($this, $method)) {
//                $events[$event] = $method;
//            }
//        }
//        return $events;
//    }

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
			$this->$name = TableRegistry::get($name);
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
			} else if ($this->SystemState->is(ARTWORK_REVIEW) || $this->SystemState->is(ARTWORK_REFINE)) {
				return $this->filterEntities($artwork);
			}
			return $artwork;
		}
	}
	
	public function assignPieces($artwork) {
		$this->PieceAssignment = $this->controller->loadComponent('PieceAssignment', ['artwork' => $artwork]);
		$this->PieceAssignment->assign();
	}
	
	/**
	 * Check and handle edition->quantity change during refine() requests
	 * 
	 * Both artworks and editions refine() methods may see changes to 
	 * the edition size. The is the method that detects if quantity 
	 * was edited. All edition types pass through this check. The 
	 * handling will be parsed out to specialized code in the 
	 * PieceAssignmentComponent if there was an edit of this value.
	 * 
	 * @param entity $artwork The full artwork stack
	 * @param integer $edition_id ID of the edition that was up for editing
	 */
	public function refinePieces($artwork, $edition_id) {
		$edition = $artwork->returnEdition($edition_id);
		$quantity_tuple = !$edition->dirty('quantity') ?
				FALSE : 
				[
					'original' => $edition->getOriginal('quantity'),
					'refinement' => $edition->quantity,
					'id' => $edition->id,
				];
		if ($quantity_tuple) {
			$this->PieceAssignment = $this->controller->loadComponent('PieceAssignment', ['artwork' => $artwork]);
			$this->PieceAssignment->refine($quantity_tuple);
		}
//			osd($artwork);
//			die;
	}
	
	
	/**
	 * Use URL query arguments to filter the Entity
	 * 
	 * 'review' views target specifics memebers of the an Artwork stack. The 
	 * URL arguments indicate which Edition and possibly which Format the 
	 * artist wants to see. The query gets everything because that is also the 
	 * source of data for the menus. This process reduces the Entity stack
	 * so the view will only have the required information.
	 * 
	 * @param Entity $artwork
	 * @return Entity
	 */
	protected function filterEntities($artwork) {
		if ($this->SystemState->isKnown('edition')) {
			$edition_id = $this->SystemState->queryArg('edition');
			$format_id = $this->SystemState->isKnown('format') ? $this->SystemState->queryArg('format') : FALSE;
			$editions = new Collection($artwork->editions);
			
			$edition_result = $editions->filter(function($edition) use ($edition_id, $format_id) {
				if ($edition->id == $edition_id) {
					if ($format_id) {
						$formats = new Collection($edition->formats);
						
						$format_result = $formats->filter(function($format) use ($format_id) {
							return $format->id == $format_id;
						});
						$edition->formats = $format_result->toArray();
					}					
					return TRUE;
				}
				return FALSE;
			});
			$artwork->editions = $edition_result->toArray();
		}
		return $artwork;
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
//		$artwork = $this->filterEntities($artwork);
		$controller = strtolower($this->SystemState->request->controller);

		if ($controller == 'editions') {
			$artwork->editions = [new \App\Model\Entity\Edition()];
		} else {
			$edition_index = $artwork->indexOfRelated('editions', $this->SystemState->queryArg('edition'));
			$artwork->editions[$edition_index]->formats = [new \App\Model\Entity\Format()];
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
	
	/**
	 * Make an entity to support Artwork stack layer creation
	 * 
	 * Creation may happen at any level and in those cases the upstream 
	 * IDs (or other data) may be required. For later 'patching' and 
	 * proper linking of new records, this allows any layer data to be
	 * passed in.
	 * 
	 * @param array $data
	 * @return \App\Model\Entity\Artwork
	 */
	public function creationStack($data = []) {
//		$image = new \App\Model\Entity\Image([]);
		$format_data = isset($data['format']) ? $data['format'] : [];
		$format = new \App\Model\Entity\Format($format_data);
		
		$edition_data = (isset($data['edition']) ? $data['edition'] : []) + 
			['formats' => [$format]];
		$edition = new \App\Model\Entity\Edition($edition_data);
		
		$artwork_data = (isset($data['artwork']) ? $data['artwork'] : []) + 
			['editions' => [$edition], /*'image'=> $image*/];
		$artwork = new \App\Model\Entity\Artwork($artwork_data);
		
		return $artwork; 
	}
	
	
//    public function buildRules(RulesChecker $rules)
//    {
//		$rules->add(function($entity, $options) {
//			osd(func_get_args());
//		}, 'QuantityRange');
//		die;
//		
//        return $rules;
//    }
	
}

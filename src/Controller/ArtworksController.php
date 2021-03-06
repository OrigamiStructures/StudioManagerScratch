<?php
namespace App\Controller;

use App\Form\ArtworkFilter;
use App\Interfaces\FilteringInterface;
use App\Lib\RequestUtility;
use App\Model\Lib\StackSet;
use App\Model\Table\ArtStacksTable;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use App\Lib\Traits\ArtReviewTrait;
use App\Model\Lib\Layer;
use App\Model\Table\ArtworksTable;
use \Cake\Network\Exception\NotFoundException;

/**
 * Artworks Controller
 *
 * @property ArtworksTable $Artworks
 */
class ArtworksController extends AppController implements FilteringInterface
{

    use ArtReviewTrait;

    public $components = ['Layers', 'Preferences'];

    public $ArtworkStack;
    public $Layers;

    public function initialize() {
        parent::initialize();
//        $this->loadComponent('ArtworkStack');
//		$this->Artworks = TableRegistry::getTableLocator()->get('Artworks');
    }

// <editor-fold defaultstate="collapsed" desc="STANDARD CRUD METHODS">
	/**
	 * Index method
	 *
	 * @return void
	     */
	public function indexOld()     {
		$this->paginate = [
			'contain' => ['Users', 'Images'],
		];
		$this->set('artworks', $this->paginate($this->Artworks));
		$this->set('_serialize', ['artworks']);
	}

	/**
	 * View method
	 *
	 * @param string|null $id Artwork id.
	 * @return void
	 * @throws NotFoundException When record not found.
	     */
	public function view($id = null)     {
		$artwork = $this->Artworks->get($id,
				[
			'contain' => ['Users', 'Images', 'Editions']
		]);

		$editions = $this->ArtworkStack->testme($id, 'artwork');


//		$editions = $this->Artworks->Editions->choiceList($id, 'artwork');
		osd($editions, 'edtions');
		$this->set('artwork', $artwork);
		$this->set('_serialize', ['artwork']);
	}

	/**
	 * Add method
	 *
	 * @return void Redirects on successful add, renders view otherwise.
	     */
	public function add()     {
		$artwork = $this->Artworks->newEntity();
		if ($this->request->is('post')) {
			$Editions = TableRegistry::getTableLocator()->get('Editions');
			$Formats = TableRegistry::getTableLocator()->get('Formats');
			$Pieces = TableRegistry::getTableLocator()->get('Pieces');
			$artwork = $this->Artworks->patchEntity($artwork, $this->request->getData());
			if ($this->Artworks->save($artwork)) {


				$edition = $Editions->newEntity([
					'quantity' => '1', 'type' => 'unique',

					'user_id' => '1', 'artwork_id' => $artwork->id
				]);
				$Editions->save($edition);


				$format = $Formats->newEntity([
					'description' => '24" x 36" oil on canvas',

					'user_id' => '1', 'edition_id' => $edition->id
				]);
				$Formats->save($format);


				$piece = $Pieces->newEntity([
					'edition_id' => $edition->id, 'format_id' => $format->id,

					'user_id' => '1', 'number' => '1', 'quantity' => '1'
				]);
				$Pieces->save($piece);


				$this->Flash->success(__('The artwork has been saved.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The artwork could not be saved. Please, try again.'));
			}
		}
		$this->request = $this->request->withData('user_id', '1');
		$users = $this->Artworks->Users->find('list', ['limit' => 200]);
		$images = $this->Artworks->Images->find('list', ['limit' => 200]);
		$this->set(compact('artwork', 'users', 'images'));
		$this->set('_serialize', ['artwork']);
	}


	/**
	 * Edit method
	 *
	 * @param string|null $id Artwork id.
	 * @return void Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	     */
	public function edit($id = null)     {
		$artwork = $this->Artworks->get($id, [
			'contain' => []
		]);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$artwork = $this->Artworks->patchEntity($artwork, $this->request->getData());
			if ($this->Artworks->save($artwork)) {
				$this->Flash->success(__('The artwork has been saved.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The artwork could not be saved. Please, try again.'));
			}
		}
		$users = $this->Artworks->Users->find('list', ['limit' => 200]);
		$images = $this->Artworks->Images->find('list', ['limit' => 200]);
		$this->set(compact('artwork', 'users', 'images'));
		$this->set('_serialize', ['artwork']);
	}

// </editor-fold>

    /**
     *
     */
    public function index()
    {
        /* @var ArtStacksTable $ArtStacks */

        $ArtStacks = TableRegistry::getTableLocator()->get('ArtStacks');
        /*
         * We may want to focus on Manager or Artist works.
         * How would that be managed here?
         */
        $supervisorCard = $this->contextUser()->getCard('supervisor');
        /*
         * Permissions would have to be considered for this next part
         */
        $artistIds = $supervisorCard
            ->getLayer('manifests')
            ->toDistinctList('member_id');

        $works = $this->Artworks->find('list')
            ->where(['user_id' => $this->contextUser()->getId('supervisor')])
            ->toArray();

        /* @var StackSet $results */

        $results = $this->paginate($ArtStacks->pageFor('artworks', array_keys($works)),
            [
                'limit' => '5',
                'scope' => 'artworks'
            ]
        );

        $Prefs = $this->Preferences->getPrefs($this->contextUser()->getId('supervisor'));
        $this->viewBuilder()->setLayout('index');
//        $this->userFilter();

        $this->set('Prefs', $Prefs);
        $this->set('results', $results);
        $this->set('indexModel', $results->getPaginatedTableName());
    }

    public function userFilter(Query $query) : Query
    {
        $modes = ['is', 'starts', 'ends', 'contains', 'isn\'t'];
        $artworkSchema = new ArtworkFilter();
        $this->set(compact('artworkSchema', 'modes'));
    }
    /**
     * Delete method
     *
     * @param string|null $id Artwork id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $artwork = $this->Artworks->get($id);
        if ($this->Artworks->delete($artwork)) {
            $this->Flash->success(__('The artwork has been deleted.'));
        } else {
            $this->Flash->error(__('The artwork could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Display one or a page of Artworks
     *
     * Single record vs multiple record will be chosen based on whether the
     * URL query value 'artwork' is set. If it is, we know the specific
     * Artwork to display. If not, we'll get a page of them (the current page).
     *
     * Also, if the Artwork is flat (has only one edition with only one
     * format) then the URL query is beefed up with the proper id data
     * and FormatController->review() is called instead.
     *
     * Later, some accomodation for Search sets must be made. That may be
     * redirected through here for rendering once the records are found
     * or it may all be handled by another method.
     */
//    public function review() {
//		$ArtStacks = TableRegistry::getTableLocator()->get('ArtStacks');
//        $records = $this->Artworks
//            ->find('all')
//            ->select(['id'])
//            ->where(['user_id' => $this->contextUser()->getId('supervisor')])
//            ->toArray();
//
//        $ids = (new Layer($records))->IDs();
//
//		$result = $this->paginate($ArtStacks->pageFor('artwork', $ids));
//
//        $Prefs = $this->Preferences->getPrefs($this->contextUser()->getId('supervisor'));
//        $this->viewBuilder()->setLayout('index');
//
//        $this->set('Prefs', $Prefs);
//        $this->set('artworks', $result);
//        $this->set('elements', $this->Layers->setElements());
//        $this->render('review');
//
//    }

    /**
     * Edit the Artwork layer and deeper layers if the work is 'flat'
     *
     * A 'flat' artwork would have one Edition possibly with one Format
     */
    public function refine() {
        $artwork = $this->ArtworkStack->stackQuery();
        if ($this->request->is('post') || $this->request->is('put')) {
    //      osd($this->request->data);die;
            $artwork = $this->Artworks->patchEntity($artwork, $this->request->getData(), [
                'associated' => ['Images', 'Editions', 'Editions.Formats', 'Editions.Formats.Images']
        ]);

        // if there is 1 edition, the quantity input could have been present
        if ($artwork->edition_count === 1) {
            $index = array_keys($this->request->getData('editions.0'));
            $deletions = $this->ArtworkStack->refinePieces($artwork,
                $this->request->getData("editions.$index.id"));
        } else {
            $deletions = [];
        }

        if ($this->ArtworkStack->refinementTransaction($artwork, $deletions)) {
//		$this->ArtworkStack->allocatePieces($artwork);
            $this->redirect(['action' => 'review', '?' => ['artwork' => $artwork->id]]);
        } else {
            $this->Flash->error(__('The artwork could not be saved. Please, try again.'));
        }
    }

        $this->ArtworkStack->layerChoiceLists();
        $this->set('artwork', $artwork);
        $this->set('elements', $this->Layers->setElements());
        $this->render('review');
    }

    public function upload() {
        $this->viewBuilder()->setLayout('ajax');
//      osd($this->request->data);die;
    }

    /**
     * Creates artwork records in element based state
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function create() {
		$artwork = $this->ArtworkStack->creationStack();
        if ($this->request->is('post') || $this->request->is('put')) {
                $artwork = $this->Artworks->patchEntity($artwork, $this->request->getData(), [
                    'associated' => [
                        'Images', 'Editions',
                        'Editions.Pieces', 'Editions.Formats',
                        'Editions.Formats.Images', 'Editions.Formats.Pieces'
                    ]
                ]);
                $this->ArtworkStack->allocatePieces($artwork);
                if ($this->ArtworkStack->refinementTransaction($artwork, [])) {
                    $this->redirect(['action' => 'review', '?' => ['artwork' => $artwork->id]]);

            } else {
               $this->Flash->error(__('The artwork could not be saved. Please, try again.'));
            }
        }
        $this->ArtworkStack->layerChoiceLists();

        $this->set('elements', $this->LayerElement->setElements());
        $this->set('artwork', $artwork);
        $this->set('_serialize', ['artwork']);
        $this->render('review');
    }

    /**
     * Simplify to UX for making unique artwork
     *
     * arrive here with a postLink and TRD that makes
     * the normal create method and form simpler.
     */
    public function createUnique() {
        $this->request = $this->request->withData('user_id', $this->contextUser()->artistId());
        $artwork = $this->create();
        $this->set('elements', $this->LayerElement->setElements());
        $this->render('review');
    }

    /**
     * Display one or a page of Artworks
     *
     * Single record vs multiple record will be chosen based on whether the
     * URL query value 'artwork' is set. If it is, we know the specific
     * Artwork to display. If not, we'll get a page of them (the current page).
     *
     * Later, some accomodation for Search sets must be made. That may be
     * redirected through here for rendering once the records are found
     * or it may all be handled by another method.
     */
    public function validateQuantities($id) {
        $this->request = $this->request->withQueryParams(['artwork' => $id]);
//      $element_management = [
//          'artwork' => 'full',
//          'edition' => 'many',
//          'format' => 'many'
//      ];
        $this->set('artwork', $this->ArtworkStack->stackQuery());
//      $this->set('element_management', $element_management);
//      $this->set('_serialize', [$artwork_variable]);
    }

    public function testMe() {
        $queries = $this->request->getData('method');
        $result = [];
        $anscestors = [];
        $disp = TableRegistry::getTableLocator()->get('Dispositions');
        $methods = $disp->customFinders();
        $options = $this->request->getData();

        if (count($queries ?? []) > 0) {
            $index = 0;
            $result = $disp->find($this->request->getData('method.' . $index++), $options);
            while ($index < count($queries)) {
            $result = $result->find($this->request->getData('method.' . $index++), $options);
            }
        }
        if (is_object($result)) {
//            $result = $disp->containAncestry($result);
            $dispositions = $result->toArray();
            $activity = new Layer($dispositions);

            $ArtStacks = TableRegistry::getTableLocator()->get('ArtStacks');
            $stacks = $ArtStacks->find('stacksFor',
                ['seed' => 'disposition', 'ids' => $activity->IDs()]);
        } else {
            $stacks = null;
            $dispositions = null;
            $activity = null;
        }

        $this->set(compact('stacks', 'result', 'methods', 'dispositions', 'activity'));
    }

    public function composeStack($flat) {
    //  foreach ($flat as $layer => $entities) {
    //      if (is_object($entities)) {
    //          $flat[$layer] = $entities->toArray();
    //      }
    //  }
        extract($flat);
        foreach ($pieces as $id => $piece) {

            if (isset($piece->disposition_pieces)) {
                $piece->disposition = [];
                foreach ($piece->disposition_pieces as $key => $dp) {
                    $piece->disposition[$dp->disposition_id] = $dispositions[$dp->disposition_id];
                }
            }
            if (is_null($piece->format_id)) {
                if (!isset($editions[$piece->edition_id]->pieces)) {
                    $editions[$piece->edition_id]->pieces = [];
                }
                $editions[$piece->edition_id]->pieces[$id] = $piece;
            } else {
                if (!isset($formats[$piece->format_id]->pieces)) {
                    $formats[$piece->format_id]->pieces = [];
                }
                $formats[$piece->format_id]->pieces[$id] = $piece;
            }

            foreach ($formats as $id => $format) {
                if (!isset($editions[$format->edition_id]->formats)) {
                    $editions[$format->edition_id]->formats = [];
                }
                $editions[$format->edition_id]->formats[$format->id] = $format;
            }

        };

        $artwork->editions = $editions;
        return $artwork;
    }

	public function editionMigration() {
		$this->relinkedPieces = [];
		$ArtStacks = TableRegistry::getTableLocator()->get('ArtStacks');
		$records = $this->Artworks
				->find('all')
				->select(['id'])
				->toArray();
		$ids = (new Layer($records))->IDs();
		$result = $ArtStacks->find('stacksFor',
			['seed' => 'artwork', 'ids' => $ids]);

		foreach ($result->load() as $artwork) {
			$this->writeFormatJoin($artwork);
		}
		$this->saveRelinkedPieces();
        $this->set('artworks', $result);
	}

	private function writeFormatJoin($artwork) {
		$EditionsFormats = TableRegistry::getTableLocator()->get('EditionsFormats');
		foreach ($artwork->formats->load() as $format) {
		$join = new \App\Model\Entity\EditionsFormat(
			[
				'change_piece' => $format->id,
				'format_id' => $format->range_flag,
				'title' => $format->title,
				'description' => $format->description,
				'edition_id' => $format->edition_id,
				'user_id' => $format->user_id,
				'image_id' => $format->image_id,
				'assigned_piece_count' => $format->assigned_piece_count,
				'fluid_piece_count' => $format->fluid_piece_count,
				'collected_piece_count' => $format->collected_piece_count,
			]
		);

			if ($EditionsFormats->save($join)) {
				$this->Flash->success("Saved f-$format->range_flag e-$format->edition_id");
				$this->relinkPieces(
						$artwork
							->find()
							->setLayer('pieces')
							->specifyFilter('format_id', $format->id)
							->load(),
						$join->id);
			} else {
				$this->Flash->error("Failed f-$format->range_flag e-$format->edition_id");
				osd($join->getErrors());
			}
		}
	}

	private function saveRelinkedPieces() {
		$PieceTable = TableRegistry::getTableLocator()->get('Pieces');
		$PieceTable->removeBehavior('CounterCache');
		foreach ($this->relinkedPieces as $piece) {
			if ($PieceTable->save($piece)) {
				$this->Flash->success("Saved p-$piece->id f-$piece->format_id");
			} else {
				$this->Flash->error("Failed p-$piece->id f-$piece->format_id");
				osd($piece->errors());
			}
		}
//		$theSave = $PieceTable->saveMany($this->relinkedPieces);
//		osd($theSave, 'result of saved pieces');
	}

	private function relinkPieces($pieces, $newFormatId) {
		foreach ($pieces as $piece) {
			$this->relinkedPieces[] = new \App\Model\Entity\Piece ([
				'id' => $piece->id,
				'format_id' => $newFormatId
			]);
		}
//		$job_lot = collection($pieces);
//		$result = $job_lot->reduce(
//				function ($accum, $piece) use ($newFormatId) {
//					return $accum[] =
//							[
//								'id' => $piece->id,
//								'format_id' => $newFormatId
//							];
//				}, []
//		);
//		$this->relinkedPieces = array_merge($this->relinkedPieces, $result);
	}

}

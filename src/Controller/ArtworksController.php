<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Table\EditionsTable;
use App\Model\Table\FormatsTable;
use App\Model\Table\PiecesTable;
use Cake\ORM\TableRegistry;
use App\Lib\Traits\ArtReviewTrait;
use App\Controller\ArtStackController;
use App\Controller\Component\LayersComponent;
use Cake\Cache\Cache;
use App\Model\Lib\IdentitySets;
use App\Model\Lib\Layer;

/**
 * Artworks Controller
 *
 * @property \App\Model\Table\ArtworksTable $Artworks
 */
class ArtworksController extends ArtStackController
{
	
    use ArtReviewTrait;

    public $components = ['ArtworkStack', 'Layers'];

    public function initialize() {
        parent::initialize();
        $this->loadComponent('ArtworkStack');
//      $this->loadComponent('Layers');
    }
	
// <editor-fold defaultstate="collapsed" desc="STANDARD CRUD METHODS">
	/**
	 * Index method
	 *
	 * @return void
	     */
	public function index()     {
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
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
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
			$Editions = TableRegistry::get('Editions');
			$Formats = TableRegistry::get('Formats');
			$Pieces = TableRegistry::get('Pieces');
			$artwork = $this->Artworks->patchEntity($artwork, $this->request->data);
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
		$this->request->data('user_id', '1');
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
			$artwork = $this->Artworks->patchEntity($artwork, $this->request->data);
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
	
    public function sample() {

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
    public function review() {
        if ($this->SystemState->urlArgIsKnown('artwork')) {
            $this->_try_flatness_redirect(
            $this->SystemState->queryArg('artwork'), 
            $this->SystemState->queryArg('edition'));
        }

        $result = $this->ArtworkStack->stackQuery();

        $this->set('artworks', $result);
        $this->set('elements', $this->Layers->setElements());
        $this->render('review');
    }
	
    /**
     * Edit the Artwork layer and deeper layers if the work is 'flat'
     * 
     * A 'flat' artwork would have one Edition possibly with one Format
     */
    public function refine() {
        $artwork = $this->ArtworkStack->stackQuery();
        if ($this->request->is('post') || $this->request->is('put')) {
    //      osd($this->request->data);die;
            $artwork = $this->Artworks->patchEntity($artwork, $this->request->data, [
                'associated' => ['Images', 'Editions', 'Editions.Formats', 'Editions.Formats.Images']
        ]);

        // if there is 1 edition, the quantity input could have been present
        if ($artwork->edition_count === 1) {
            $index = array_keys($this->request->data['editions'])[0];
            $deletions = $this->ArtworkStack->refinePieces($artwork, 
            $this->request->data['editions'][$index]['id']);
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
        $this->viewBuilder()->layout('ajax');
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
                $artwork = $this->Artworks->patchEntity($artwork, $this->request->data, [
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
        $this->request->data += ['user_id' => $this->SystemState->artistId()];
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
	$this->request->query = ['artwork' => $id];
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
		
//		$intNumberSet = [1,2,3,4,5];
//		$symNumberSet = ['A','B','C','D','E'];
//        $reqs = new \App\Lib\RenumberRequests($symNumberSet);
////		$requests = [[1,2],[2,3],[3,1]];
////		$requests = [[1,2],[3,4]];
////		$data = ['A'=>'B','B'=>'C','C'=>'B','D'=>'A','E'=>'B'];
//		$data = ['A'=>'B','C'=>'D'];
//		foreach($data as $on => $nn) {
//			$reqs->insert(new \App\Lib\RenumberRequest($on, $nn));
//		}
////		foreach ($requests as $request) {
////			$reqs->insert(new \App\Lib\RenumberRequest($request[0],$request[1]));
////		}
//		osd($reqs->_explicit_providers, 'explicit providers');
//		osd($reqs->_explicit_receivers, 'explicit receiver');
//		osd($reqs->messagePackage());
//		osd($reqs->_receiver_checklist, 'receiver checklist');
//		osd($reqs->_provider_checklist, 'providers checklist');
//		die;

        $queries = $this->request->data('method');
        $result = [];
        $anscestors = [];
        $disp = TableRegistry::get('Dispositions');
        $methods = $disp->customFinders();
        $options = $this->request->data;
        
        if (count($queries) > 0) {
            $index = 0;
            $result = $disp->find($this->request->data['method'][$index++], $options);
            while ($index < count($queries)) {
            $result = $result->find($this->request->data['method'][$index++], $options);
            }
        }
        if (is_object($result)) {
//            $result = $disp->containAncestry($result);
            $dispositions = $result->toArray();
            $activity = new Layer($dispositions);
            
            $ArtStacks = TableRegistry::getTableLocator()->get('ArtStacks');
            $stacks = $ArtStacks->find('stackFrom', 
                ['layer' => 'disposition', 'ids' => $activity->IDs()]);
			
			osd(count($stacks->all()));
            
//            $sorted = [];
//            foreach ($dispositions as $disposition) {
//                    $sorted[$disposition->id] = $disposition;
//            }
//            $dispositions = $sorted;
//            $pieces = new IdentitySets('Pieces', $dispositions);
//            $pieces->arrayResult();
//            $editions = new IdentitySets('Editions', $pieces->entity());
//            $editions->arrayResult();
//            $formats = new IdentitySets('Formats', $pieces->entity());
//            $formats->arrayResult();
//            $artworks = new IdentitySets('Artworks', $editions->entity());
//            $artworks->arrayResult();
//            osd($t->result());
        }

//		osd($dispLayer);die;
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
	
}

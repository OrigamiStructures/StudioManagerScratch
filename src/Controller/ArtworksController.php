<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Table\EditionsTable;
use App\Model\Table\FormatsTable;
use App\Model\Table\PiecesTable;
use Cake\ORM\TableRegistry;

/**
 * Artworks Controller
 *
 * @property \App\Model\Table\ArtworksTable $Artworks
 */
class ArtworksController extends AppController
{

//	public $components = ['ArtworkStack'];
	
	public function initialize() {
		parent::initialize();
		$this->loadComponent('ArtworkStack');
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
	 * Later, some accomodation for Search sets must be made. That may be  
	 * redirected through here for rendering once the records are found 
	 * or it may all be handled by another method.
	 */
    public function review() {
		if ($this->SystemState->isKnown('artwork')) {
//			$artwork_element = 'full';
			$artwork_variable = 'artwork';
		} else {
//			$artwork_element = 'many';
			$artwork_variable = 'artworks';
		}
//        $element_management = [
//            'artwork' => $artwork_element,
//            'edition' => 'many',
//            'format' => 'many'
//        ];
        $this->set($artwork_variable, $this->ArtworkStack->stackQuery());
//        $this->set('element_management', $element_management);
        $this->set('_serialize', [$artwork_variable]);
    }
	
	/**
	 * Edit the Artwork layer and deeper layers if the work is 'flat'
	 * 
	 * A 'flat' artwork would have one Edition possibly with one Format
	 */
	public function refine() {
		$artwork = $this->ArtworkStack->stackQuery();
        if ($this->request->is('post') || $this->request->is('put')) {
			$artwork = $this->Artworks->patchEntity($artwork, $this->request->data, [
				'associated' => ['Images', 'Editions', 'Editions.Formats', 'Editions.Formats.Images']
			]);
			
			// if there is 1 edition, the quantity input could have been present
			if ($artwork->edition_count === 1) {
				$index = array_keys($this->request->data['editions'])[0];
				$deletions = $this->ArtworkStack->refinePieces($artwork, 
						$this->request->data['editions'][$index]['id']);
			}	
//			osd($artwork);die;
			
			$deletions = $this->ArtworkStack->refinePieces($artwork, $this->request->data);

			if ($this->ArtworkStack->refinementTransaction($artwork, $deletions)) {
//				$this->ArtworkStack->allocatePieces($artwork);
                $this->redirect(['action' => 'review', '?' => ['artwork' => $artwork->id]]);
            } else {
                $this->Flash->error(__('The artwork could not be saved. Please, try again.'));
            }
        }
		
		$this->ArtworkStack->layerChoiceLists();
		$this->set('artwork', $artwork);
//		$this->set('element_management', $element_management);
		$this->render('create');
	}
	
    /**
     * Creates artwork records in element based state
     * 
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function create() {
//		osd($this->request->data, 'trd');
		$artwork = $this->ArtworkStack->creationStack(); 
        if ($this->request->is('post') || $this->request->is('put')) {
			$artwork = $this->Artworks->patchEntity($artwork, $this->request->data, [
				'associated' => [
					/*'Images', */'Editions', 
						'Editions.Pieces', 'Editions.Formats', 
							'Editions.Formats.Images', 'Editions.Formats.Pieces'
					]
			]);
			$this->ArtworkStack->allocatePieces($artwork);
//			osd($artwork);die('ready to go, artwork controller submitted');
            if ($this->Artworks->save($artwork)) {
                $this->redirect(['action' => 'review', '?' => ['artwork' => $artwork->id]]);
            } else {
               $this->Flash->error(__('The artwork could not be saved. Please, try again.'));
            }
        }

		$this->ArtworkStack->layerChoiceLists();
        
		$this->set('artwork', $artwork);
        $this->set('_serialize', ['artwork']);
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
        $element_management = [
            'artwork' => 'full',
            'edition' => 'many',
            'format' => 'many'
        ];
        $this->set('artwork', $this->ArtworkStack->stackQuery());
        $this->set('element_management', $element_management);
//        $this->set('_serialize', [$artwork_variable]);
    }
	
}

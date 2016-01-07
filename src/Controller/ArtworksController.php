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
			$artwork_element = 'full';
			$artwork_variable = 'artwork';
		} else {
			$artwork_element = 'many';
			$artwork_variable = 'artworks';
		}
        $element_management = [
            'artwork' => $artwork_element,
            'edition' => 'many',
            'format' => 'many'
        ];
        $this->set($artwork_variable, $this->ArtworkStack->stackQuery());
        $this->set('element_management', $element_management);
        $this->set('_serialize', [$artwork_variable]);
    }
	
	public function refine() {
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Artworks->saveStack($this->request->data)) {
                $this->redirect(['action' => 'elementTest']);
            } else {
                $this->Flash->error(__('The artwork could not be saved. Please, try again.'));
            }
        }
		
		$artwork = $this->ArtworkStack->stackQuery();
		if (count($artwork['editions']) > 1) {
			// many editions
			$element_management = [
				'artwork' => 'fieldset',
				'edition' => 'many',
				'format' => 'many',
			];
		} else {
			$element_management = [
				'artwork' => 'fieldset',
				'edition' => 'fieldset',
				'format' => 'fieldset',
			];
		}
		
		$this->ArtworkStack->layerChoiceLists();
		$this->set('artwork', $artwork);
		$this->set('element_management', $element_management);
		$this->render('create');
	}
	
    /**
     * Creates artwork records in element based state
     * 
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function create() {
		$artwork = new \App\Model\Entity\Artwork();
        if ($this->request->is('post') || $this->request->is('put')) {
			$this->SystemState->changeTo(ARTWORK_SAVE);
//			$artwork = $this->Artworks->patchEntity($artwork, $this->request->data, 
//				['associated' => $this->ArtworkStack->fullContainment]);
//			osd($artwork);//die;
            if ($this->Artworks->saveStack($this->request->data) || true) {
//            if ($this->Artworks->save($artwork)) {
//				osd($artwork);
				die;
                $this->redirect(['action' => 'review', '?' => ['artwork' => $artwork->id]]);
            } else {
                $this->Flash->error(__('The artwork could not be saved. Please, try again.'));
            }
        }
		
        $element_management = [
            'artwork' => 'fieldset',
            'edition' => 'fieldset',
            'format' => 'fieldset'
        ];
		$this->ArtworkStack->layerChoiceLists();
        
        $this->set(compact('artwork', 'element_management'));
        $this->set('_serialize', ['artwork']);
    }
	
}

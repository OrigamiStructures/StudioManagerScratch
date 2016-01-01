<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Formats Controller
 *
 * @property \App\Model\Table\FormatsTable $Formats
 */
class FormatsController extends AppController
{

	public $components = ['ArtworkStack'];

// <editor-fold defaultstate="collapsed" desc="STANDARD CRUD">
	/**
	 * Index method
	 *
	 * @return void
	     */
	public function index()     {
		$this->paginate = [
			'contain' => ['Users', 'Images', 'Editions', 'Subscriptions']
		];
		$this->set('formats', $this->paginate($this->Formats));
		$this->set('_serialize', ['formats']);
	}

	/**
	 * View method
	 *
	 * @param string|null $id Format id.
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	     */
	public function view($id = null)     {
		$format = $this->Formats->get($id,
				[
			'contain' => ['Users', 'Images', 'Editions', 'Subscriptions', 'Pieces']
		]);
		$this->set('format', $format);
		$this->set('_serialize', ['format']);
	}

	/**
	 * Add method
	 *
	 * @return void Redirects on successful add, renders view otherwise.
	     */
	public function add()     {
		$format = $this->Formats->newEntity();
		if ($this->request->is('post')) {
			$format = $this->Formats->patchEntity($format, $this->request->data);
			if ($this->Formats->save($format)) {
				$this->Flash->success(__('The format has been saved.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The format could not be saved. Please, try again.'));
			}
		}
		$users = $this->Formats->Users->find('list', ['limit' => 200]);
		$images = $this->Formats->Images->find('list', ['limit' => 200]);
		$editions = $this->Formats->Editions->find('list', ['limit' => 200]);
		$subscriptions = $this->Formats->Subscriptions->find('list', ['limit' => 200]);
		$this->set(compact('format', 'users', 'images', 'editions', 'subscriptions'));
		$this->set('_serialize', ['format']);
	}

	/**
	 * Edit method
	 *
	 * @param string|null $id Format id.
	 * @return void Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	     */
	public function edit($id = null)     {
		$format = $this->Formats->get($id, [
			'contain' => []
		]);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$format = $this->Formats->patchEntity($format, $this->request->data);
			if ($this->Formats->save($format)) {
				$this->Flash->success(__('The format has been saved.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The format could not be saved. Please, try again.'));
			}
		}
		$users = $this->Formats->Users->find('list', ['limit' => 200]);
		$images = $this->Formats->Images->find('list', ['limit' => 200]);
		$editions = $this->Formats->Editions->find('list', ['limit' => 200]);
		$subscriptions = $this->Formats->Subscriptions->find('list', ['limit' => 200]);
		$this->set(compact('format', 'users', 'images', 'editions', 'subscriptions'));
		$this->set('_serialize', ['format']);
	}

// </editor-fold>

	/**
     * Delete method
     *
     * @param string|null $id Format id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $format = $this->Formats->get($id);
        if ($this->Formats->delete($format)) {
            $this->Flash->success(__('The format has been deleted.'));
        } else {
            $this->Flash->error(__('The format could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
	
	public function refine() {
//		osd($this->request->data);
		$artwork = $this->ArtworkStack->stackQuery();
//		$id = $this->SystemState->queryArg('artwork');
//		$artwork = $this->Editions->Artworks->get($id, ['contain' => ['Editions' => ['Formats']]]);
//		osd($artwork);
		$element_management = [
			'artwork' => 'describe',
			'edition' => 'describe',
			'format' => 'fieldset',
		];
		$this->set('element_management', $element_management);
//		$this->set('artworks', [$artwork]);
		$this->set('artwork', $artwork);
		$this->ArtworkStack->layerChoiceLists();
		$this->render('/Artworks/create');
//		osd($artwork);die;
	}
	
	public function create() {
		$artwork = $this->ArtworkStack->stackQuery();
		$element_management = [
			'artwork' => 'describe',
			'edition' => 'describe',
			'format' => 'fieldset',
		];
		$this->set('element_management', $element_management);
		$this->set('artwork', $artwork);
		$this->ArtworkStack->layerChoiceLists();
		$this->render('/Artworks/create');		
	}
	
}

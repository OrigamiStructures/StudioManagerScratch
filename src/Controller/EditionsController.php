<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Editions Controller
 *
 * @property \App\Model\Table\EditionsTable $Editions
 */
class EditionsController extends AppController
{
	
	public $components = ['ArtworkStack'];
	
	/**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Artworks', 'Series']
        ];
        $this->set('editions', $this->paginate($this->Editions));
        $this->set('_serialize', ['editions']);
    }

    /**
     * View method
     *
     * @param string|null $id Edition id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $edition = $this->Editions->get($id, [
            'contain' => ['Users', 'Artworks', 'Series', 'Formats', 'Pieces']
        ]);
        $this->set('edition', $edition);
        $this->set('_serialize', ['edition']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $edition = $this->Editions->newEntity();
        if ($this->request->is('post')) {
            $edition = $this->Editions->patchEntity($edition, $this->request->data);
            if ($this->Editions->save($edition)) {
                $this->Flash->success(__('The edition has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The edition could not be saved. Please, try again.'));
            }
        }
        $users = $this->Editions->Users->find('list', ['limit' => 200]);
        $artworks = $this->Editions->Artworks->find('list', ['limit' => 200]);
        $series = $this->Editions->Series->find('list', ['limit' => 200]);
        $this->set(compact('edition', 'users', 'artworks', 'series'));
        $this->set('_serialize', ['edition']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Edition id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $edition = $this->Editions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $edition = $this->Editions->patchEntity($edition, $this->request->data);
            if ($this->Editions->save($edition)) {
                $this->Flash->success(__('The edition has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The edition could not be saved. Please, try again.'));
            }
        }
        $users = $this->Editions->Users->find('list', ['limit' => 200]);
        $artworks = $this->Editions->Artworks->find('list', ['limit' => 200]);
        $series = $this->Editions->Series->find('list', ['limit' => 200]);
        $this->set(compact('edition', 'users', 'artworks', 'series'));
        $this->set('_serialize', ['edition']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Edition id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $edition = $this->Editions->get($id);
        if ($this->Editions->delete($edition)) {
            $this->Flash->success(__('The edition has been deleted.'));
        } else {
            $this->Flash->error(__('The edition could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
	
	public function create() {
//		osd($this->request->data);
		$id = $this->request->params['named']['artwork'];
		$artwork = $this->Editions->Artworks->get($id, ['contain' => ['Editions' => ['Formats']]]);
//		osd($artwork);
		$element_management = [
			'artwork' => 'describe',
			'edition' => 'fieldset',
			'format' => 'fieldset',
		];
		$this->set('element_management', $element_management);
		$this->set('artworks', [$artwork]);
		$this->set('artwork', $artwork);
		$this->ArtworkStack->layerChoiceLists();
		$this->render('/Artworks/create');
//		osd($artwork);die;
	}
	
}

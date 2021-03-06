<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use App\Lib\Traits\ArtReviewTrait;
use App\Controller\Component\LayersComponent;

/**
 * Formats Controller
 *
 * @property \App\Model\Table\FormatsTable $Formats
 */
class FormatsController extends AppController
{

	use ArtReviewTrait;

	public $components = ['ArtworkStack', 'Layers'];

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
			$format = $this->Formats->patchEntity($format, $this->request->getData());
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
			$format = $this->Formats->patchEntity($format, $this->request->getData());
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

	/**
	 * Show detailed view of a single Format
	 *
	 * Provides Artwork and Edition summaries, full explanation of
	 * the Format, and full Piece display with basic information
	 *
	 * Driven by artwork, edition, format IDs in URL query
	 */
	public function review() {
		$artwork = $this->ArtworkStack->stackQuery();
		$this->set('artworks', $artwork);
		$this->set('elements', $this->Layers->setElements());
		$this->render('/Artworks/review');
	}

	public function testMe() {
		$conditions = [11, 12, 13, 14, 25, 26];
		$contain = ['Pieces' => ['Dispositions'], 'Editions' => ['Artworks']];
		$formats = $this->Formats->find()
				->where(['Formats.id IN' => $conditions])
				->contain($contain);
		$this->set('formats', $formats);
//		osd($formats->toArray());
	}

}

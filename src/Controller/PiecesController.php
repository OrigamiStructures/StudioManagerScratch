<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Pieces Controller
 *
 * @property \App\Model\Table\PiecesTable $Pieces
 */
class PiecesController extends AppController
{
	
	public $components = ['ArtworkStack'];

// <editor-fold defaultstate="collapsed" desc="STANDARD CRUD">
	/**
	 * Index method
	 *
	 * @return void
	     */
	public function index() {
		$conditions = [];
		if ($this->SystemState->isKnown('format')) {
			$conditions = ['Pieces.format_id' => $this->SystemState->queryArg('format')];
		} elseif ($this->SystemState->isKnown('edition')) {
			$conditions = ['Pieces.edition_id' => $this->SystemState->queryArg('edition')];
		} elseif ($this->SystemState->isKnown('artwork')) {
			$conditions = ['Artworks.id' => $this->SystemState->queryArg('artwork')];
		}
		$query = $this->Pieces->find('all')
				->where($conditions)
				->contain(['Users', 'Editions', 'Formats', 'Editions.Artworks']);
		$this->set('pieces', $this->paginate($query));
		$this->set('_serialize', ['pieces']);
	}

	/**
	 * View method
	 *
	 * @param string|null $id Piece id.
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	     */
	public function view($id = null)     {
		$piece = $this->Pieces->get($id,
				[
			'contain' => ['Users', 'Editions', 'Formats', 'Dispositions']
		]);
		$this->set('piece', $piece);
		$this->set('_serialize', ['piece']);
	}

	/**
	 * Add method
	 *
	 * @return void Redirects on successful add, renders view otherwise.
	     */
	public function add()     {
		$piece = $this->Pieces->newEntity();
		if ($this->request->is('post')) {
			$piece = $this->Pieces->patchEntity($piece, $this->request->data);
			if ($this->Pieces->save($piece)) {
				$this->Flash->success(__('The piece has been saved.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The piece could not be saved. Please, try again.'));
			}
		}
		$users = $this->Pieces->Users->find('list', ['limit' => 200]);
		$editions = $this->Pieces->Editions->find('list', ['limit' => 200]);
		$formats = $this->Pieces->Formats->find('list', ['limit' => 200]);
		$this->set(compact('piece', 'users', 'editions', 'formats'));
		$this->set('_serialize', ['piece']);
	}

	/**
	 * Edit method
	 *
	 * @param string|null $id Piece id.
	 * @return void Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	     */
	public function edit($id = null)     {
		$piece = $this->Pieces->get($id, [
			'contain' => []
		]);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$piece = $this->Pieces->patchEntity($piece, $this->request->data);
			if ($this->Pieces->save($piece)) {
				$this->Flash->success(__('The piece has been saved.'));
				$artwork_id = $this->Pieces->Editions->get($piece->edition_id, [
					'select' => ['artwork_id']
				])->artwork_id;
				return $this->redirect(['controller' => 'artworks', 'action' => 'validate_quantities', $artwork_id]);
			} else {
				$this->Flash->error(__('The piece could not be saved. Please, try again.'));
			}
		}
		$users = $this->Pieces->Users->find('list', ['valueField' => 'username', 'limit' => 200]);
		$editions = $this->Pieces->Editions->find('list', ['valueField' => 'title', 'limit' => 200])
				->where(['Editions.id' => $piece->edition_id]);

		$formats = $this->Pieces->Formats->find('list', ['valueField' => 'title', 'limit' => 200])
				->where(['Formats.edition_id' => $piece->edition_id]);
		$this->set(compact('piece', 'users', 'editions', 'formats'));
		$this->set('_serialize', ['piece']);
	}

// </editor-fold>
	
	/**
     * Delete method
     *
     * @param string|null $id Piece id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $piece = $this->Pieces->get($id);
        if ($this->Pieces->delete($piece)) {
            $this->Flash->success(__('The piece has been deleted.'));
        } else {
            $this->Flash->error(__('The piece could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
	
	public function review() {
		
	}
	
	public function renumber() {
		$artwork = $this->ArtworkStack->stackQuery();
		$this->ArtworkStack->layerChoiceLists();
		$this->set('artwork', $artwork);
	}
}

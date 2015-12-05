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

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Editions', 'Formats']
        ];
        $this->set('pieces', $this->paginate($this->Pieces));
        $this->set('_serialize', ['pieces']);
    }

    /**
     * View method
     *
     * @param string|null $id Piece id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $piece = $this->Pieces->get($id, [
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
    public function add()
    {
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
    public function edit($id = null)
    {
        $piece = $this->Pieces->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
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
}

<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Dispositions Controller
 *
 * @property \App\Model\Table\DispositionsTable $Dispositions
 */
class DispositionsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Members', 'Locations', 'Pieces']
        ];
        $this->set('dispositions', $this->paginate($this->Dispositions));
        $this->set('_serialize', ['dispositions']);
    }

    /**
     * View method
     *
     * @param string|null $id Disposition id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $disposition = $this->Dispositions->get($id, [
            'contain' => ['Users', 'Members', 'Locations', 'Pieces']
        ]);
        $this->set('disposition', $disposition);
        $this->set('_serialize', ['disposition']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $disposition = $this->Dispositions->newEntity();
        if ($this->request->is('post')) {
            $disposition = $this->Dispositions->patchEntity($disposition, $this->request->data);
            if ($this->Dispositions->save($disposition)) {
                $this->Flash->success(__('The disposition has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The disposition could not be saved. Please, try again.'));
            }
        }
        $users = $this->Dispositions->Users->find('list', ['limit' => 200]);
        $members = $this->Dispositions->Members->find('list', ['limit' => 200]);
        $locations = $this->Dispositions->Locations->find('list', ['limit' => 200]);
        $pieces = $this->Dispositions->Pieces->find('list', ['limit' => 200]);
        $this->set(compact('disposition', 'users', 'members', 'locations', 'pieces'));
        $this->set('_serialize', ['disposition']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Disposition id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $disposition = $this->Dispositions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $disposition = $this->Dispositions->patchEntity($disposition, $this->request->data);
            if ($this->Dispositions->save($disposition)) {
                $this->Flash->success(__('The disposition has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The disposition could not be saved. Please, try again.'));
            }
        }
        $users = $this->Dispositions->Users->find('list', ['limit' => 200]);
        $members = $this->Dispositions->Members->find('list', ['limit' => 200]);
        $locations = $this->Dispositions->Locations->find('list', ['limit' => 200]);
        $pieces = $this->Dispositions->Pieces->find('list', ['limit' => 200]);
        $this->set(compact('disposition', 'users', 'members', 'locations', 'pieces'));
        $this->set('_serialize', ['disposition']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Disposition id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $disposition = $this->Dispositions->get($id);
        if ($this->Dispositions->delete($disposition)) {
            $this->Flash->success(__('The disposition has been deleted.'));
        } else {
            $this->Flash->error(__('The disposition could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}

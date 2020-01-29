<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Shares Controller
 *
 * @property \App\Model\Table\SharesTable $Shares
 *
 * @method \App\Model\Entity\Share[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SharesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Supervisors', 'Managers', 'Categories']
        ];
        $shares = $this->paginate($this->Shares);

        $this->set(compact('shares'));
    }

    /**
     * View method
     *
     * @param string|null $id Share id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $share = $this->Shares->get($id, [
            'contain' => ['Supervisors', 'Managers', 'Categories']
        ]);

        $this->set('share', $share);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $share = $this->Shares->newEntity();
        if ($this->request->is('post')) {
            $share = $this->Shares->patchEntity($share, $this->request->getData());
            if ($this->Shares->save($share)) {
                $this->Flash->success(__('The share has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The share could not be saved. Please, try again.'));
        }
        $supervisors = $this->Shares->Supervisors->find('list', ['limit' => 200]);
        $managers = $this->Shares->Managers->find('list', ['limit' => 200]);
        $categories = $this->Shares->Categories->find('list', ['limit' => 200]);
        $this->set(compact('share', 'supervisors', 'managers', 'categories'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Share id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $share = $this->Shares->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $share = $this->Shares->patchEntity($share, $this->request->getData());
            if ($this->Shares->save($share)) {
                $this->Flash->success(__('The share has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The share could not be saved. Please, try again.'));
        }
        $supervisors = $this->Shares->Supervisors->find('list', ['limit' => 200]);
        $managers = $this->Shares->Managers->find('list', ['limit' => 200]);
        $categories = $this->Shares->Categories->find('list', ['limit' => 200]);
        $this->set(compact('share', 'supervisors', 'managers', 'categories'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Share id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $share = $this->Shares->get($id);
        if ($this->Shares->delete($share)) {
            $this->Flash->success(__('The share has been deleted.'));
        } else {
            $this->Flash->error(__('The share could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

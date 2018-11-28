<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Artists Controller
 *
 * @property \App\Model\Table\ArtistsTable $Artists
 *
 * @method \App\Model\Entity\Artist[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ArtistsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Members', 'MemberUsers']
        ];
        $artists = $this->paginate($this->Artists);

        $this->set(compact('artists'));
    }

    /**
     * View method
     *
     * @param string|null $id Artist id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $artist = $this->Artists->get($id, [
            'contain' => ['Members', 'MemberUsers', 'Users']
        ]);

        $this->set('artist', $artist);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $artist = $this->Artists->newEntity();
        if ($this->request->is('post')) {
            $artist = $this->Artists->patchEntity($artist, $this->request->getData());
            if ($this->Artists->save($artist)) {
                $this->Flash->success(__('The artist has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The artist could not be saved. Please, try again.'));
        }
        $members = $this->Artists->Members->find('list', ['limit' => 200]);
        $memberUsers = $this->Artists->MemberUsers->find('list', ['limit' => 200]);
        $this->set(compact('artist', 'members', 'memberUsers'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Artist id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $artist = $this->Artists->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $artist = $this->Artists->patchEntity($artist, $this->request->getData());
            if ($this->Artists->save($artist)) {
                $this->Flash->success(__('The artist has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The artist could not be saved. Please, try again.'));
        }
        $members = $this->Artists->Members->find('list', ['limit' => 200]);
        $memberUsers = $this->Artists->MemberUsers->find('list', ['limit' => 200]);
        $this->set(compact('artist', 'members', 'memberUsers'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Artist id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $artist = $this->Artists->get($id);
        if ($this->Artists->delete($artist)) {
            $this->Flash->success(__('The artist has been deleted.'));
        } else {
            $this->Flash->error(__('The artist could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

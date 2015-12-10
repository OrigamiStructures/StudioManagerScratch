<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * GroupsMembers Controller
 *
 * @property \App\Model\Table\GroupsMembersTable $GroupsMembers
 */
class GroupsMembersController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Groups', 'Members']
        ];
        $this->set('groupsMembers', $this->paginate($this->GroupsMembers));
        $this->set('_serialize', ['groupsMembers']);
    }

    /**
     * View method
     *
     * @param string|null $id Groups Member id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $groupsMember = $this->GroupsMembers->get($id, [
            'contain' => ['Users', 'Groups', 'Members']
        ]);
        $this->set('groupsMember', $groupsMember);
        $this->set('_serialize', ['groupsMember']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $groupsMember = $this->GroupsMembers->newEntity();
        if ($this->request->is('post')) {
            $groupsMember = $this->GroupsMembers->patchEntity($groupsMember, $this->request->data);
            if ($this->GroupsMembers->save($groupsMember)) {
                $this->Flash->success(__('The groups member has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The groups member could not be saved. Please, try again.'));
            }
        }
        $users = $this->GroupsMembers->Users->find('list', ['limit' => 200]);
        $groups = $this->GroupsMembers->Groups->find('list', ['limit' => 200]);
        $members = $this->GroupsMembers->Members->find('list', ['limit' => 200]);
        $this->set(compact('groupsMember', 'users', 'groups', 'members'));
        $this->set('_serialize', ['groupsMember']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Groups Member id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $groupsMember = $this->GroupsMembers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $groupsMember = $this->GroupsMembers->patchEntity($groupsMember, $this->request->data);
            if ($this->GroupsMembers->save($groupsMember)) {
                $this->Flash->success(__('The groups member has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The groups member could not be saved. Please, try again.'));
            }
        }
        $users = $this->GroupsMembers->Users->find('list', ['limit' => 200]);
        $groups = $this->GroupsMembers->Groups->find('list', ['limit' => 200]);
        $members = $this->GroupsMembers->Members->find('list', ['limit' => 200]);
        $this->set(compact('groupsMember', 'users', 'groups', 'members'));
        $this->set('_serialize', ['groupsMember']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Groups Member id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $groupsMember = $this->GroupsMembers->get($id);
        if ($this->GroupsMembers->delete($groupsMember)) {
            $this->Flash->success(__('The groups member has been deleted.'));
        } else {
            $this->Flash->error(__('The groups member could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}

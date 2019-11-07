<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Hash;

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
            $groupsMember = $this->GroupsMembers->patchEntity($groupsMember, $this->request->getData());
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
            $groupsMember = $this->GroupsMembers->patchEntity($groupsMember, $this->request->getData());
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
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete()
    {
        $this->refererStack($this->referer());
        $conditions = [
            'member_id' => Hash::get($this->request->getQueryParams(), 'member'),
            'group_id' => Hash::get($this->request->getQueryParams(), 'group'),
        ];

        if ($this->GroupsMembers->deleteAll($conditions)) {
            $this->Flash->success(__('The member has been removed from the group.'));
        } else {
            $this->Flash->error(__('The member could not be removed from the group. Please, try again.'));
        }
        return $this->redirect($this->refererStack(SYSTEM_CONSUME_REFERER));
    }
}

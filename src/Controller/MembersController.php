<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Members Controller
 *
 * @property \App\Model\Table\MembersTable $Members
 */
class MembersController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Images']
        ];
        $this->set('members', $this->paginate($this->Members));
        $this->set('_serialize', ['members']);
    }

    /**
     * View method
     *
     * @param string|null $id Member id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $member = $this->Members->get($id, [
            'contain' => ['Images', 'Groups', 'Users', 'Dispositions', 'Locations']
        ]);
        $this->set('member', $member);
        $this->set('_serialize', ['member']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $member = $this->Members->newEntity();
        if ($this->request->is('post')) {
            $member = $this->Members->patchEntity($member, $this->request->data);
            if ($this->Members->save($member)) {
                $this->Flash->success(__('The member has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The member could not be saved. Please, try again.'));
            }
        }
        $images = $this->Members->Images->find('list', ['limit' => 200]);
        $groups = $this->Members->Groups->find('list', ['limit' => 200]);
        $this->set(compact('member', 'images', 'groups'));
        $this->set('_serialize', ['member']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Member id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $member = $this->Members->get($id, [
            'contain' => ['Groups']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $member = $this->Members->patchEntity($member, $this->request->data);
            if ($this->Members->save($member)) {
                $this->Flash->success(__('The member has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The member could not be saved. Please, try again.'));
            }
        }
        $images = $this->Members->Images->find('list', ['limit' => 200]);
        $groups = $this->Members->Groups->find('list', ['limit' => 200]);
        $this->set(compact('member', 'images', 'groups'));
        $this->set('_serialize', ['member']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Member id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $member = $this->Members->get($id);
        if ($this->Members->delete($member)) {
            $this->Flash->success(__('The member has been deleted.'));
        } else {
            $this->Flash->error(__('The member could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
    
    /**
     * Creates member records in element based state
     * 
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function create($type) {
		$member = new \App\Model\Entity\Member();
        $member = $this->Members->completeMemberEntity($member, $type);
        if ($this->request->is('post') || $this->request->is('put')) {
            $member = $this->Members->patchEntity($member, $this->request->data);
            if ($this->Members->save($member)) {
                $this->Flash->success(__('The member has been saved.'));
                return $this->redirect(['action' => 'review']);
            } else {
                $this->Flash->error(__('The member could not be saved. Please, try again.'));
            }
        }
        $types = ['Institution', 'Person', 'User', 'Category'];
        $types = array_combine($types, $types);
        $this->set(compact('member', 'element_management', 'types'));
        $this->set('_serialize', ['member']);
    }
	
	/**
	 * Display one or a page of Members
	 * 
	 * Single record vs multiple records will be chosen based on whether the 
	 * URL query value 'member' is set. If it is, we know the specific 
	 * Member to display. If not, we'll get a page of them (the current page). 
	 * 
	 * Later, some accomodation for Search sets must be made. That may be  
	 * redirected through here for rendering once the records are found 
	 * or it may all be handled by another method.
	 */
    public function review() {
        $query = $this->Members->find('memberReview');
        $query->contain(['Addresses', 'Contacts', 'Groups']);
        $query->orderAsc('last_name');
        $this->set('members', $this->paginate($query));
        $this->set('_serialize', ['members']);
    }
    
    /**
     * Edit any member record, based upon the 'member' query argument
     * 
     */
    public function refine() {
        $referrer = $this->referer();
        if(!$this->SystemState->isKnown('member')){
            $this->Flash->error(__('You must provide a single member id to edit'));
            return $this->redirect($this->referer());
        }
        $member = $this->Members->get($this->SystemState->queryArg('member'), [
            'contain' => ['Addresses', 'Contacts', 'Groups']
        ]);
        $member = $this->Members->completeMemberEntity($member, $member->type);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $member = $this->Members->patchEntity($member, $this->request->data);
            if ($this->Members->save($member)) {
                $this->Flash->success(__('The member has been saved.'));
                return $this->redirect(['action' => "review", '?' => ['member' => $member->id]]);
            } else {
                $this->Flash->error(__('The member could not be saved. Please, try again.'));
            }
        }
        $types = ['Institution', 'Person', 'User', 'Category'];
        $types = array_combine($types, $types);
        $this->set(compact('member', 'element_management', 'types', 'referrer'));
        $this->set('_serialize', ['member']);
        $this->render('create');

    }
}

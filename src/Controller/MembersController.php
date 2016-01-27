<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Inflector;

/**
 * Members Controller
 *
 * @property \App\Model\Table\MembersTable $Members
 */
class MembersController extends AppController
{
    private $_memberTypes = [
        MEMBER_TYPE_INSTITUTION, MEMBER_TYPE_INSTITUTION,
        MEMBER_TYPE_PERSON, MEMBER_TYPE_PERSON,
        MEMBER_TYPE_USER, MEMBER_TYPE_USER,
        MEMBER_TYPE_CATEGORY, MEMBER_TYPE_CATEGORY
    ];
    
    public function initialize() {
        parent::initialize();
        $this->set('member_types', $this->_memberTypes);
    }

// <editor-fold defaultstate="collapsed" desc="CRUD Methods">
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
    public function view($id = null)     {
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
    public function add()     {
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
    public function edit($id = null)     {
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
    public function delete($id = null)     {
        $this->request->allowMethod(['post', 'delete']);
        $member = $this->Members->get($id);
        if ($this->Members->delete($member)) {
            $this->Flash->success(__('The member has been deleted.'));
        } else {
            $this->Flash->error(__('The member could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    // </editor-fold>
    
    /**
     * Creates member records in element based state
     * 
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function create($type) {
		$member = new \App\Model\Entity\Member();
        $member = $this->Members->defaultMemberEntity($member, $type);
        if ($this->request->is('post') || $this->request->is('put')) {
            $member = $this->Members->patchEntity($member, $this->request->data);
            if ($this->Members->save($member)) {
                $this->Flash->success(__('The member has been saved.'));
                return $this->redirect(['action' => 'review', '?' => ['member' => $member->id]]);
            } else {
                $this->Flash->error(__('The member could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('member'));
        $this->set('_serialize', ['member']);
    }
    
    /**
     * Add contact or address
     * 
     * With 'contacts' or 'addresses' in the type, add the respective new element to the
     * return
     * 
     * @param string $entity_type 'contacts' or 'addresses'
     */
    public function addElement($entity_type) {
        if(!in_array($entity_type, ['contacts', 'addresses'])){
            throw new \BadMethodCallException('Entity type must be either contacts or addresses');
        }
        $table = Inflector::pluralize(Inflector::classify($entity_type));
        $member = new \App\Model\Entity\Member($this->request->data);
        
        $start = count($member->$entity_type);
        
        $member->$entity_type = $member->$entity_type + $this->Members->$table->spawn(1, [], $start);
        
        $this->set('member', $member);
        $this->set('_serialize', ['member']);
        $this->render('create');
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
        $this->SystemState->referer($this->referer());
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
        if(!$this->SystemState->isKnown('member')){
            $this->Flash->error(__('You must provide a single member id to edit'));
            return $this->redirect($this->SystemState->referer(SYSTEM_CONSUME_REFERER));
        }
        
        $member = $this->Members->find('memberReview')->toArray()[0];
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $member = $this->Members->patchEntity($member, $this->request->data);
            if ($this->Members->save($member)) {
                $this->Flash->success(__('The member has been saved.'));
                return $this->redirect($this->SystemState->referer(SYSTEM_CONSUME_REFERER));
            } else {
                $this->Flash->error(__('The member could not be saved. Please, try again.'));
            }
        }
        if(empty($member->errors())){
            $this->SystemState->referer($this->referer());
        }
        $this->set('member', $member);
        $this->set('_serialize', ['member']);
        $this->render('create');

    }
}

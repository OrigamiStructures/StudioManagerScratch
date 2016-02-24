<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Inflector;
use Cake\Core\Configure;
use Cake\Controller\Component\CookieComponent;
use Cake\Collection\Collection;
use App\Model\Entity\Member;

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
        $this->loadComponent('Cookie');
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

    // </editor-fold>

    /**
     * Delete method
     *
     * @param string|null $id Member id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)     {
        $this->request->allowMethod(['post', 'delete']);
        $this->SystemState->referer($this->referer());
        $member = $this->Members->get($id);
        //Update relationships to remove many to many relation records
        $this->Members->hasMany('GroupsMembers', [
            'foreignKey' => 'member_id',
            'dependent' => TRUE
        ]);

        if ($this->Members->delete($member)) {
            $this->Flash->success(__('The member has been deleted.'));
        } else {
            $this->Flash->error(__('The member could not be deleted. Please, try again.'));
        }
        return $this->redirect($this->SystemState->referer(SYSTEM_CONSUME_REFERER));
    }

    
    /**
     * Creates member records in element based state
     * 
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function create($type) {
		$member = new \App\Model\Entity\Member();
        if ($this->request->is('post') || $this->request->is('put')) {
            $member = $this->Members->patchEntity($member, $this->request->data);
            if ($this->Members->save($member)) {
                $this->Flash->success(__('The member has been saved.'));
                return $this->redirect(['action' => 'review', '?' => ['member' => $member->id]]);
            } else {
                $this->Flash->error(__('The member could not be saved. Please, try again.'));
            }
        }
        $member = $this->Members->defaultMemberEntity($member, $type);
        $this->set(compact('member'));
        $this->set('_serialize', ['member']);
        $this->render('review');
    }
    
    /**
     * Add contact or address
     * 
     * With 'contacts', 'addresses', or 'groups' in the type, add the respective 
     * new element to the return
     * 
     * @param string $entity_type 'contacts', 'addresses', or 'groups'
     */
    public function addElement($entity_type) {
        if(!in_array($entity_type, ['contacts', 'addresses', 'groups'])){
            throw new \BadMethodCallException('Entity type must be either contacts or addresses');
        }

		$table = Inflector::pluralize(Inflector::classify($entity_type));
        $entity_name = Inflector::classify($entity_type);
        $entity = "\App\Model\Entity\\$entity_name";
        $member = new \App\Model\Entity\Member();
        $member = $this->Members->patchEntity($member, $this->request->data);
        
        $start = count($member->$entity_type);
//        osd($member->$entity_type);die;
        
        $additional_object = new Collection($this->Members->$table->spawn(1,[], $start));
        $additional_object = $additional_object->map(function($value) use ($entity){
            return new $entity($value);
        });
                
        $member->$entity_type = (!empty($member->$entity_type) ? $member->$entity_type : []) + $additional_object->toArray();
        
        $this->set('member', $member);
        $this->set('_serialize', ['member']);
        $this->render('review');
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
        $this->retreiveAndSetGroups();
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
        $this->retreiveAndSetGroups();
        $this->set('member', $member);
        $this->set('_serialize', ['member']);
        $this->render('review');
    }
    
    /**
     * Find all groups associated with this member and set them to viewVars
     * 
     */
    private function retreiveAndSetGroups() {
        $member_groups = $this->Members->Groups->find('memberGroups');
        $groups_list = (new Collection($member_groups))->combine('id', 'displayTitle');
        $this->set('member_groups', $member_groups);
        $this->set('groups', $groups_list);
    }
    
    public function testMe() {
        $cookie_name = Configure::read('Users.RememberMe.Cookie.name');
        $cookie = $this->Cookie->read($cookie_name);
        osd($cookie, 'the cookie');
        osd($this->request->session()->read(), 'session before');
        $this->request->session()->destroy();
        osd($this->request->session()->read(), 'session after destroy');
    }
}

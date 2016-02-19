<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Collection\Collection;

/**
 * Dispositions Controller
 *
 * @property \App\Model\Table\DispositionsTable $Dispositions
 */
class DispositionsController extends AppController
{
	
	public $DispositionManager;
	
	/**
	 * create() will lead the user through evolving screens. This names the next one
	 *
	 * @var string
	 */
	protected $_view_name = 'create';


	/**
	 * Manage redirects for disposition activities
	 * 
	 * All visits to this controller will eventually return to the original 
	 * page. Even if the artist is locked here for several calls, the original 
	 * page will be remembered and eventually they will be returned there.
	 * 
	 * @param \Cake\Event\Event $event
	 */
	public function beforeFilter(\Cake\Event\Event $event) {
		parent::beforeFilter($event);
		
		if (!stristr($this->request->referer(), DS . 'dispositions' . DS)) {
			$this->SystemState->referer($this->request->referer());
		}
	}

	public function initialize() {
		parent::initialize();
		$this->loadComponent('DispositionManager');
	}

// <editor-fold defaultstate="collapsed" desc="STANDARD CRUD METHODS">
	/**
	 * Index method
	 *
	 * @return void
	     */
	public function index()     {
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
	public function view($id = null)     {
		$disposition = $this->Dispositions->get($id,
				[
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
	public function add()     {
		$disposition = $this->Dispositions->newEntity();
		if ($this->request->is('post')) {
			$disposition = $this->Dispositions->patchEntity($disposition,
					$this->request->data);
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
	public function edit($id = null)     {
		$disposition = $this->Dispositions->get($id,
				[
			'contain' => []
		]);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$disposition = $this->Dispositions->patchEntity($disposition,
					$this->request->data);
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

// </editor-fold>
	
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
	
	/**
	 * Create a new disposition
	 * 
	 * Disposition types MIGHT filter pieces to an appropriate subset (maybe even 
	 * members could be filtered). The filters COULD run both ways. But for now 
	 * this doesn't look viable.
	 * 
	 * Any combination of member_id, piece_id or disposition->type 
	 * may be known on entry. Additionally, artwork_id, edition_id and 
	 * format_id will be sent if known. These extra three  may not be needed 
	 * for create so this should be re-evaluated.
	 * 
	 */
	public function create() {
		$disposition = $this->DispositionManager->get();
		$this->DispositionManager->merge($disposition, $this->SystemState->queryArg());
		if ($this->request->is('post')) {
			$disposition = $this->Dispositions->patchEntity($disposition, $this->request->data);
			if ($this->postComplete($disposition)) {
				$this->autoRender = FALSE;
				$this->redirect($this->SystemState->referer(SYSTEM_CONSUME_REFERER));
			}
		}
		$labels = $this->Dispositions->disposition_label;
		$this->set(compact('disposition', 'labels'));
		$this->render($this->_view_name);
	}
	
	/**
	 * In the future this can implement more complex rules
	 * 
	 * @param entity $disposition
	 * @return boolean
	 */
	public function postComplete($disposition) {
		$status = true;
		if (empty($disposition->type)) {
			if ($this->Dispositions->map($disposition->label)) {
				$disposition->type = $this->Dispositions->map($disposition->label);
			} else {
				$status = FALSE;
			}
		}
		if ($disposition->type === DISPOSITION_LOAN && is_null($disposition->end_date)) {
			$this->_view_name = 'loan';
			$status = FALSE;
		}
		$this->DispositionManager->write();
		return $status;
	}
	
	public function refine() {
		$disposition = $this->DispositionManager->get();
		$this->DispositionManager->merge($disposition, $this->SystemState->queryArg());
		$this->autoRender = false;
		$this->redirect($this->SystemState->referer(SYSTEM_CONSUME_REFERER));	
	}
	
	/**
	 * Dump the evolving disposition without saving it
	 */
	public function discard() {
		$this->DispositionManager->discard();
		$this->autoRender = false;
		$this->redirect($this->SystemState->referer(SYSTEM_CONSUME_REFERER));			
	}
	
	/**
	 * Retain only the indicated address among many
	 * 
	 * There are several circumstances where several addresses could 
	 * be possibilities for the dispo. They many show as links and clicking 
	 * on one will come here to make that the final choice.
	 */
	public function chooseAddress() {
		$disposition = $this->DispositionManager->get();
		$collection = new Collection($disposition->addresses);
		$address_id = $this->SystemState->queryArg('address');
		$choice = $collection->filter(function($address) use($address_id){
			return $address->id == $address_id;
		});
		$this->DispositionManager->disposition->addresses = $choice->toArray();
		$this->DispositionManager->write();
		$this->autoRender = false;
		$this->redirect($this->SystemState->referer(SYSTEM_CONSUME_REFERER));			
	}
}

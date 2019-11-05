<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Collection\Collection;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

/**
 * Dispositions Controller
 *
 * @property \App\Model\Table\DispositionsTable $Dispositions
 */
class DispositionsController extends AppController
{

	public $DispositionManager;

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
			$this->refererStack($this->request->referer());
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
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Members']
        ];
        $dispositions = $this->paginate($this->Dispositions);

        $this->set(compact('dispositions'));
        $this->set('_serialize', ['dispositions']);
    }

    /**
     * View method
     *
     * @param string|null $id Disposition id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $disposition = $this->Dispositions->get($id, [
            'contain' => ['Users', 'Members', 'Pieces']
        ]);

        $this->set('disposition', $disposition);
        $this->set('_serialize', ['disposition']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $disposition = $this->Dispositions->newEntity();
        if ($this->request->is('post')) {
            $disposition = $this->Dispositions->patchEntity($disposition, $this->request->getData());
            if ($this->Dispositions->save($disposition)) {
                $this->Flash->success(__('The disposition has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The disposition could not be saved. Please, try again.'));
            }
        }
        $users = $this->Dispositions->Users->find('list', ['limit' => 200]);
        $members = $this->Dispositions->Members->find('list', ['limit' => 200]);
        $pieces = $this->Dispositions->Pieces->find('list', ['limit' => 200]);
        $this->set(compact('disposition', 'users', 'members', 'pieces'));
        $this->set('_serialize', ['disposition']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Disposition id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $disposition = $this->Dispositions->get($id, [
            'contain' => ['Pieces']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $disposition = $this->Dispositions->patchEntity($disposition, $this->request->getData());
            if ($this->Dispositions->save($disposition)) {
                $this->Flash->success(__('The disposition has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The disposition could not be saved. Please, try again.'));
            }
        }
        $users = $this->Dispositions->Users->find('list', ['limit' => 200]);
        $members = $this->Dispositions->Members->find('list', ['limit' => 200]);
        $pieces = $this->Dispositions->Pieces->find('list', ['limit' => 200]);
        $this->set(compact('disposition', 'users', 'members', 'pieces'));
        $this->set('_serialize', ['disposition']);
    }

// </editor-fold>

    /**
     * Delete method
     *
     * @param string|null $id Disposition id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
		$this->Dispositions->hasMany('DispositionsPieces', [
            'foreignKey' => 'disposition_id'
        ]);
        $disposition = $this->Dispositions->get($id, ['contain' => ['Pieces']]);
//		osd($disposition);die;
		$result = $this->Dispositions->connection()->transactional(function () use ($disposition) {
			$result = TRUE;
			$result = $result && $this->Dispositions->delete($disposition);

			return $result;
		});
        if ($result) {
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
		$errors = [];
		$disposition = $this->DispositionManager->get();
		$this->DispositionManager->merge($disposition, $this->request->getQueryParams());

		if ($this->request->is('post')) {

		    /*
		     * I'm not sure how to replace the entire request data array in the new codebase.
		     * withData() only seems to set one value. And I'm not sure if withBody() is the right alternative.
		     */
			$this->request->data = $this->completeRule($this->request->getData());

			$disposition = $this->Dispositions->patchEntity($disposition, $this->request->getData());
			$this->DispositionManager->write();
			$errors = $disposition->getErrors();
//			$this->Dispositions->checkRules($disposition);
			if (empty($errors)) {
				$this->autoRender = FALSE;
				$this->redirect($this->refererStack(SYSTEM_CONSUME_REFERER));
			}
		}
		$labels = $this->Dispositions->labels();
		$this->set(compact('disposition', 'labels', 'errors'));
	}

	/**
	 * Refine an evolving or existing Disposition
	 *
	 *
	 */
	public function refine() {
	    $disposition_id = Hash::get($this->request->getQueryParams(), 'disposition');
//		osd($disposition_id);
		if (is_null($disposition_id)) {
			$disposition = $this->DispositionManager->get();
//			die('wrong place');
		} else {
			$disposition = $this->Dispositions->get($disposition_id, [
				'contain' => ['Pieces' => [
					'Editions' => ['Artworks', 'Pieces'],
					'Formats' => [
						'Editions' => ['Artworks', 'Pieces']]
					],
					'Members',
					'Addresses',
					'Dispositions']
			]);
			$disposition->addresses = [$disposition->address];
			$this->DispositionManager->write($disposition);
		}
		$this->DispositionManager->merge($disposition, $this->request->getQueryParams());

		$this->autoRender = false;
		$this->redirect($this->refererStack(SYSTEM_CONSUME_REFERER));
	}

	/**
	 * Refine an existing Disposition
	 */
	protected function _refine() {

	}

		/**
	 * Dump the evolving disposition without saving it
	 *
	 * Since some piece assignment changes are made during the disposition
	 * creation process to keep the choices accurate in the display as the
	 * artist proceeds, some changes will need to be 'unmade' but others
	 * will remain because the original state will be lost (and only
	 * of slight interest).
	 *
	 * Open edition piece configuration will be restored to 'one record for
	 * pieces at each state'.
	 *
	 * Piece assignment will remain in any newly assigned condition.
	 */
	public function discard() {
		$this->DispositionManager->discard();
		$this->autoRender = false;
		$this->redirect($this->refererStack(SYSTEM_CONSUME_REFERER));
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
		$address_id = Hash::get($this->request->getQueryParams(), 'address');
		$choice = $collection->filter(function($address) use($address_id){
			return $address->id == $address_id;
		});
		$this->DispositionManager->disposition->addresses = $choice->toArray();
		$this->DispositionManager->write();
		$this->autoRender = false;
		$this->redirect($this->refererStack(SYSTEM_CONSUME_REFERER));
	}

	/**
	 * Remove piece from the disposition
	 *
	 * @param type $element
	 */
	public function remove() {
		$this->DispositionManager->remove();
		$this->autoRender = false;
		$this->redirect($this->refererStack(SYSTEM_CONSUME_REFERER));
	}

	/**
	 * Automatically set the 'complete' value if dates force the issue
	 *
	 * @param array $data
	 * @return array
	 */
	public function completeRule($data) {
		$pattern = '%s/%s/%s';
		if (empty($data['end_date'])) {
			$data['end_date'] = $data['start_date'];
		}
		$date = $data['end_date'];
		$timestamp = strtotime(sprintf($pattern, $date['month'], $date['day'], $date['year']));
//		osd($date);
//		osd($timestamp);
//		osd(time());
//		osd($timestamp > time());die;
		if ((boolean) $data['complete'] && $timestamp > time()) {
			$data['complete'] = 0;
		}
		return $data;
	}

	/**
	 * Save the fully defined dispositon
	 *
	 * The disposition at this point is fully an object so that the construction
	 * process could be managed efficiently. This save process converts the parts
	 * into arrays and patches them into a new entity that will be properly
	 * constructed for the save. Member and Address data, previously separate
	 * entities, will now be column data on the disposition. This data will
	 * stand as the member/address snapshot. The disposition also links to the
	 * Member so that record can have an ongoing history of all activity.
	 */
	public function save() {
		$disposition = $this->DispositionManager->get();
		$address = array_pop($disposition->addresses)->toArray();
		unset($disposition->addresses);

		$data = [
			'user_id' => $this->contextUser()->artistId(),
			'member_id' => $disposition->member->id,
			'address_id' => $address['id'],
			] + $disposition->toArray();
		$member_data = array_intersect_key($data['member'], ['first_name' => NULL, 'last_name' => NULL]);
		$address_data = array_intersect_key($address, [
			'address1' => NULL,
			'address2' => NULL,
			'address3' => NULL,
			'city' => NULL,
			'state' => NULL,
			'zip' => NULL,
			'country' => NULL,
			]);

		$entity = $this->Dispositions->newEntity($data);
		$entity->setDirty('member', FALSE);
		$entity->setDirty('addresses', FALSE);
		$entity = $this->Dispositions->patchEntity($entity, $member_data, ['validate' => FALSE]);
		$entity = $this->Dispositions->patchEntity($entity, $address_data, ['validate' => FALSE]);

		if($this->Dispositions->save($entity)){
			$pieces = $entity->pieces;
			foreach ($pieces as $piece) {
				Cache::delete("get_default_artworks[_{$piece->format['edition']['artwork']['id']}_]", 'artwork');
			}
			Cache::delete($this->contextUser()->artistId(), 'dispo');
		} else {
			$this->Flash->error('The disposition could not be saved. Please try again.');
		}

		$this->autoRender = false;
		$this->redirect($this->refererStack(SYSTEM_CONSUME_REFERER));
	}

}

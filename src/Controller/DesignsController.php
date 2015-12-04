<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Form\DispositionForm;

/**
 * Designs Controller
 *
 * @property \App\Model\Table\DesignsTable $Designs
 */
class DesignsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('designs', $this->paginate($this->Designs));
        $this->set('_serialize', ['designs']);
    }

    /**
     * View method
     *
     * @param string|null $id Design id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $design = $this->Designs->get($id, [
            'contain' => []
        ]);
        $this->set('design', $design);
        $this->set('_serialize', ['design']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $design = $this->Designs->newEntity();
        if ($this->request->is('post')) {
            $design = $this->Designs->patchEntity($design, $this->request->data);
            if ($this->Designs->save($design)) {
                $this->Flash->success(__('The design has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The design could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('design'));
        $this->set('_serialize', ['design']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Design id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $design = $this->Designs->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $design = $this->Designs->patchEntity($design, $this->request->data);
            if ($this->Designs->save($design)) {
                $this->Flash->success(__('The design has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The design could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('design'));
        $this->set('_serialize', ['design']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Design id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $design = $this->Designs->get($id);
        if ($this->Designs->delete($design)) {
            $this->Flash->success(__('The design has been deleted.'));
        } else {
            $this->Flash->error(__('The design could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
	
	public function newEvent($type = '') {
		$disposition = New DispositionForm();
		$this->request->data = [
			'multiple_venues' => FALSE,
			'venues' => '',
			'return_to' => '',
			'storage' => '',
			'multiple_pieces' => FALSE,
			'pieces' => '',
			'documents' => '',
			'destination' => '',
			'review' => '',
		];
		
		$storages = ['storage one', 'storage two'];
		$return_to = ['Home', 'Storage', 'Review'];
		$venues = ['venue one', 'venue two'];
		$pieces = ['piece one', 'piece two'];
		$documents = ['documnent 1', 'document 2'];
		$destination = '';
		$review = '';
		
		$this->set(compact([
			'storages', 'return_to', 'venues', 'documents', 
			'pieces', 'destination', 'review', 'disposition' 
		]));
	}
}

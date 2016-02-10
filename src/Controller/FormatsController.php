<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Formats Controller
 *
 * @property \App\Model\Table\FormatsTable $Formats
 */
class FormatsController extends AppController
{

	public $components = ['ArtworkStack'];

// <editor-fold defaultstate="collapsed" desc="STANDARD CRUD">
	/**
	 * Index method
	 *
	 * @return void
	     */
	public function index()     {
		$this->paginate = [
			'contain' => ['Users', 'Images', 'Editions', 'Subscriptions']
		];
		$this->set('formats', $this->paginate($this->Formats));
		$this->set('_serialize', ['formats']);
	}

	/**
	 * View method
	 *
	 * @param string|null $id Format id.
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	     */
	public function view($id = null)     {
		$format = $this->Formats->get($id,
				[
			'contain' => ['Users', 'Images', 'Editions', 'Subscriptions', 'Pieces']
		]);
		$this->set('format', $format);
		$this->set('_serialize', ['format']);
	}

	/**
	 * Add method
	 *
	 * @return void Redirects on successful add, renders view otherwise.
	     */
	public function add()     {
		$format = $this->Formats->newEntity();
		if ($this->request->is('post')) {
			$format = $this->Formats->patchEntity($format, $this->request->data);
			if ($this->Formats->save($format)) {
				$this->Flash->success(__('The format has been saved.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The format could not be saved. Please, try again.'));
			}
		}
		$users = $this->Formats->Users->find('list', ['limit' => 200]);
		$images = $this->Formats->Images->find('list', ['limit' => 200]);
		$editions = $this->Formats->Editions->find('list', ['limit' => 200]);
		$subscriptions = $this->Formats->Subscriptions->find('list', ['limit' => 200]);
		$this->set(compact('format', 'users', 'images', 'editions', 'subscriptions'));
		$this->set('_serialize', ['format']);
	}

	/**
	 * Edit method
	 *
	 * @param string|null $id Format id.
	 * @return void Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	     */
	public function edit($id = null)     {
		$format = $this->Formats->get($id, [
			'contain' => []
		]);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$format = $this->Formats->patchEntity($format, $this->request->data);
			if ($this->Formats->save($format)) {
				$this->Flash->success(__('The format has been saved.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The format could not be saved. Please, try again.'));
			}
		}
		$users = $this->Formats->Users->find('list', ['limit' => 200]);
		$images = $this->Formats->Images->find('list', ['limit' => 200]);
		$editions = $this->Formats->Editions->find('list', ['limit' => 200]);
		$subscriptions = $this->Formats->Subscriptions->find('list', ['limit' => 200]);
		$this->set(compact('format', 'users', 'images', 'editions', 'subscriptions'));
		$this->set('_serialize', ['format']);
	}

// </editor-fold>

	/**
     * Delete method
     *
     * @param string|null $id Format id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $format = $this->Formats->get($id);
        if ($this->Formats->delete($format)) {
            $this->Flash->success(__('The format has been deleted.'));
        } else {
            $this->Flash->error(__('The format could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
	
	public function review() {
		$artwork = $this->ArtworkStack->stackQuery();
		$this->ArtworkStack->layerChoiceLists();
		$element_management = [
			'artwork' => 'full',
			'edition' => 'many',
			'format' => 'many',
		];
		$this->set('element_management', $element_management);
		$this->set('artwork', $artwork);
		$this->render('/Artworks/review');
	}
	
	/**
	 * Refine that data for a single Format
	 * 
	 * The artwork and edition will show as 'reference' info on the page.
	 * 
	 * SAVE HAS NOT BEEN WRITTEN
	 * 
	 */
	public function refine() {
		$this->Artworks = TableRegistry::get('Artworks');
		$artwork = $this->ArtworkStack->stackQuery();
        if ($this->request->is('post') || $this->request->is('put')) {
			$artwork = $this->Artworks->patchEntity($artwork, $this->request->data, [
				'associated' => ['Editions.Formats.Images']
			]);
            if ($this->Artworks->save($artwork)) {
                $this->redirect([
					'controller' => 'artworks', 
					'action' => 'review',
					'?' => [
						'artwork' => $this->SystemState->queryArg('artwork'),
						'format' => $this->SystemState->queryArg('edition')
					]]);
            } else {
                $this->Flash->error(__('The format could not be saved. Please, try again.'));
            }
        }
		
//		$artwork = $this->ArtworkStack->stackQuery();
//		$element_management = [
//			'artwork' => 'describe',
//			'edition' => 'describe',
//			'format' => 'fieldset',
//		];
//		$this->set('element_management', $element_management);
		$this->set('artwork', $artwork);
		$this->ArtworkStack->layerChoiceLists();
		$this->render('/Artworks/create');
	}
	
	/**
	 * Create an new Format
	 * 
	 * The artwork and edition will be shown as reference info on the page
	 * 
	 * SAVE HAS NOT BEEN WRITTEN
	 * 
	 */
	public function create() {
//		osd($this->request->data, 'trd');
		$this->Artworks = TableRegistry::get('Artworks');
		$artwork = $this->ArtworkStack->stackQuery();
        if ($this->request->is('post') || $this->request->is('put')) {
			$artwork = $this->Artworks->patchEntity($artwork, $this->request->data, [
				'associated' => ['Editions', 'Editions.Formats', 'Editions.Formats.Images']
			]);
//			osd($artwork, 'artwork for format submit');die;
            if ($this->Artworks->save($artwork)) {
                $this->redirect([
					'controller' => 'artworks', 
					'action' => 'review',
					'?' => [
						'artwork' => $this->SystemState->queryArg('artwork'),
						'format' => $this->SystemState->queryArg('edition')
					]]);
            } else {
                $this->Flash->error(__('The format could not be saved. Please, try again.'));
            }
        }
		
//		$element_management = [
//			'artwork' => 'describe',
//			'edition' => 'describe',
//			'format' => 'fieldset',
//		];
//		$this->set('element_management', $element_management);
		$this->set('artwork', $artwork);
		$this->ArtworkStack->layerChoiceLists();
		$this->render('/Artworks/review');		
	}
	
}

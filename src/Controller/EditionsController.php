<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Collection\Collection;

/**
 * Editions Controller
 *
 * @property \App\Model\Table\EditionsTable $Editions
 */
class EditionsController extends AppController
{
	
	public $components = ['ArtworkStack'];
	
	
	public function initialize() {
		parent::initialize();
		$this->loadComponent('ArtworkStack');
	}

// <editor-fold defaultstate="collapsed" desc="BASIC CRUD">
	/**
	 * Index method
	 *
	 * @return void
	     */
	public function index()     {
		$this->paginate = [
			'contain' => ['Users', 'Artworks', 'Series']
		];
		$this->set('editions', $this->paginate($this->Editions));
		$this->set('_serialize', ['editions']);
	}

	/**
	 * View method
	 *
	 * @param string|null $id Edition id.
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	     */
	public function view($id = null)     {
		$edition = $this->Editions->get($id,
				[
			'contain' => ['Users', 'Artworks', 'Series', 'Formats', 'Pieces']
		]);
		$this->set('edition', $edition);
		$this->set('_serialize', ['edition']);
	}

	/**
	 * Add method
	 *
	 * @return void Redirects on successful add, renders view otherwise.
	     */
	public function add()     {
		$edition = $this->Editions->newEntity();
		if ($this->request->is('post')) {
			$edition = $this->Editions->patchEntity($edition, $this->request->data);
			if ($this->Editions->save($edition)) {
				$this->Flash->success(__('The edition has been saved.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The edition could not be saved. Please, try again.'));
			}
		}
		$users = $this->Editions->Users->find('list', ['limit' => 200]);
		$artworks = $this->Editions->Artworks->find('list', ['limit' => 200]);
		$series = $this->Editions->Series->find('list', ['limit' => 200]);
		$this->set(compact('edition', 'users', 'artworks', 'series'));
		$this->set('_serialize', ['edition']);
	}

	/**
	 * Edit method
	 *
	 * @param string|null $id Edition id.
	 * @return void Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	     */
	public function edit($id = null)     {
		$edition = $this->Editions->get($id, [
			'contain' => []
		]);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$edition = $this->Editions->patchEntity($edition, $this->request->data);
			if ($this->Editions->save($edition)) {
				$this->Flash->success(__('The edition has been saved.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The edition could not be saved. Please, try again.'));
			}
		}
		$users = $this->Editions->Users->find('list', ['limit' => 200]);
		$artworks = $this->Editions->Artworks->find('list', ['limit' => 200]);
		$series = $this->Editions->Series->find('list', ['limit' => 200]);
		$this->set(compact('edition', 'users', 'artworks', 'series'));
		$this->set('_serialize', ['edition']);
	}

// </editor-fold>

	/**
     * Delete method
     *
     * @param string|null $id Edition id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $edition = $this->Editions->get($id);
        if ($this->Editions->delete($edition)) {
            $this->Flash->success(__('The edition has been deleted.'));
        } else {
            $this->Flash->error(__('The edition could not be deleted. Please, try again.'));
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
	 * Refine that data for a single Edition
	 * 
	 * The artwork will show as 'reference' info on the page. The fate of 
	 * multiple Formats has not been resolved. The first will show in a 
	 * fieldset for editing but subsiquent Formats are also in the data.
	 * 
	 * THIS ASSUMES 1 EDITION IN THE FORM, ALWAYS THE FIRST
	 * 
	 */
	public function refine() {
		$this->Artworks = TableRegistry::get('Artworks');
		$artwork = $this->ArtworkStack->stackQuery();
        if ($this->request->is('post') || $this->request->is('put')) {
//			$this->ArtworkStack->addRefinementRules();

			$artwork = $this->Artworks->patchEntity($artwork, $this->request->data, [
				'associated' => ['Editions', 'Editions.Formats', 'Editions.Formats.Images']
			]);
			$index = array_keys($this->request->data['editions'])[0];
			$deletions = $this->ArtworkStack->refinePieces($artwork, 
					$this->request->data['editions'][$index]['id']);
//			osd($artwork);die;
//			die;

			if ($this->ArtworkStack->refinementTransaction($artwork, $deletions)) {
                $this->Flash->success(__('The edition has been changed.'));
                $this->redirect(['controller' => 'editions', 'action' => 'review', '?' => [
					'artwork' => $this->SystemState->queryArg('artwork'),
					'edition' => $this->SystemState->queryArg('edition')
						]]);
            } else {
                $this->Flash->error(__('The edition could not be saved. Please, try again.'));
            }
        }
		
		$this->ArtworkStack->layerChoiceLists();
		$this->set('artwork', $artwork);
		$this->render('/Artworks/create_dev');
	}
	
	/**
	 * Create an new Edition and a single Format for it
	 * 
	 * The artwork will be shown as reference info on the page
	 * 
	 * SAVE HAS NOT BEEN WRITTEN
	 * 
	 */
	public function create() {
		$this->Artworks = TableRegistry::get('Artworks');
		
		$artwork = $this->ArtworkStack->stackQuery();
        if ($this->request->is('post') || $this->request->is('put')) {
			$artwork = $this->Artworks->patchEntity($artwork, $this->request->data, [
				'associated' => ['Editions', 'Editions.Formats', 'Editions.Pieces', 'Editions.Formats.Images']
			]);
			$this->ArtworkStack->allocatePieces($artwork);
			
            if ($this->Artworks->save($artwork)) {
                $this->redirect([
					'controller' => 'editions', 
					'action' => 'review', 
					'?' => ['artwork' => $this->SystemState->queryArg('artwork')]
				]);
            } else {
                $this->Flash->error(__('The edition could not be saved. Please, try again.'));
            }
        }
		
		$this->ArtworkStack->layerChoiceLists();
		$this->set('artwork', $artwork);
		$this->render('/Artworks/create_dev');		
	}
	
	public function assign() {
		if (!$this->SystemState->isKnown('artwork')) {
			$this->Flash->error(__('No artwork was identified so no piece assignment can be done.'));
			$this->redirect($this->SystemState->referer());
		}
		$edition_id = $this->SystemState->queryArg('edition');
		$conditions = $this->SystemState->buildConditions(['edition' => 'id']);
		
		// Edition 
		//	with unassigned Pieces
		//  and Formats
		//   with fluid Pieces
		$edition = $this->Editions->find()->where($conditions)
				->contain(['Pieces' => function($q) use($edition_id) {
					return $q
						->where(['edition_id' => $edition_id, 'format_id IS NULL']);
				}])
				->contain('Formats')
				->contain(['Formats.Pieces' => function($q) use($edition_id) {
					return $q
						->where(['disposition_count' => 0]);
				}]);
		$pieces = $this->Editions->Pieces->find()->where($this->SystemState->buildConditions(['edition']));
//		$pieces 
//		osd($edition->toArray());
		$this->set('edition', $edition->toArray());
		$this->set('pieces', new Collection($pieces->toArray()));
		
	}
	
}

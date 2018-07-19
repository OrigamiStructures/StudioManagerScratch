<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Collection\Collection;
use App\Form\AssignmentForm;
use Cake\View\Form\FormContext;
use App\Lib\Traits\ArtReviewTrait;
use App\Controller\ArtStackController;
use App\Controller\Component\LayersComponent;

/**
 * Editions Controller
 *
 * @property \App\Model\Table\EditionsTable $Editions
 */
class EditionsController extends ArtStackController
{
	
	use ArtReviewTrait;
	
	public $components = ['ArtworkStack', 'Layers'];
	
	
	public function initialize() {		
		parent::initialize();
//		$this->loadComponent('ArtworkStack');
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
	
	/**
	 * Display an Edition, its Artwork, and its Format(s)
	 * 
	 * If the edition is found to be flat (only one format) 
	 * the format id is added to the query and rendering 
	 * is redirected to FormatController->review()
	 */
	public function review() {
		$this->_try_flatness_redirect(
				$this->SystemState->queryArg('artwork'), 
				$this->SystemState->queryArg('edition'));
		
		$artwork = $this->ArtworkStack->stackQuery();
		
		$this->set('artwork', $artwork);
		$this->set('elements', $this->LayerElement->setElements());
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
		$this->set('elements', $this->LayerElement->setElements());
		$this->render('/Artworks/review');
	}
	
	/**
	 * Create an new Edition and a single Format for it
	 * 
	 * The artwork will be shown as reference info on the page
	 */
	public function create() {
//		osd($this->request->data, 'trd');
		$this->Artworks = TableRegistry::get('Artworks');
		
		$artwork = $this->ArtworkStack->stackQuery();
        if ($this->request->is('post') || $this->request->is('put')) {
			$artwork = $this->Artworks->patchEntity($artwork, $this->request->data, [
				'associated' => ['Editions', 'Editions.Formats', 'Editions.Pieces', 'Editions.Formats.Images']
			]);
			$this->ArtworkStack->allocatePieces($artwork);
//			osd($artwork, 'after adding pieces'); die;
			if ($this->ArtworkStack->refinementTransaction($artwork, [])) {
                $this->redirect([
					'controller' => 'artworks', 
					'action' => 'review', 
					'?' => [
						'artwork' => $this->SystemState->queryArg('artwork'),
						]
				]);
            } else {
                $this->Flash->error(__('The edition could not be saved. Please, try again.'));
            }
        }
		
		$this->ArtworkStack->layerChoiceLists();
		$this->set('artwork', $artwork);
		$this->set('elements', $this->LayerElement->setElements());
		$this->render('/Artworks/review');
	}
	
	public function assign() {
		$this->SystemState->referer($this->SystemState->referer());
		if (!$this->SystemState->urlArgIsKnown('artwork')) {
			$this->Flash->error(__('No artwork was identified so no piece assignment can be done.'));
			$this->redirect($this->SystemState->referer());
		}
		$errors = [];
		
		$EditionStack = $this->loadComponent('EditionStack');
		$data = $EditionStack->stackQuery();
		extract($data); // providers, pieces
		
		$assignment = new AssignmentForm($data['providers']);
		$assign = new FormContext($this->request, $this->request->data);
		
        if ($this->request->is('post') || $this->request->is('put')) {
			if ($assignment->execute($this->request->data)) {
				if($this->EditionStack->reassignPieces($assignment, $providers)) {
					$this->Flash->error(__('The reassignments were completed.'));
// https://github.com/OrigamiStructures/StudioManagerScratch/issues/63 
// and issue 24
					// on success, try triggering an event that fixes all counter cache values
					// here are to approches. The second one would need a new listener action
					// because the one mentioned is tied into Cache behavior and its context
//					$this->_refreshFormatCounterCaches($this->request->data);
//					$this->dispatchEvent('Pieces.fluidFormatPieces');
//					die;
					$this->redirect($this->SystemState->referer(SYSTEM_CONSUME_REFERER));
				} else {
					$this->Flash->error(__('There was a problem reassigning the pieces. Please try again'));
				}

			} else {
				// have use correct input errors
				$errors= $assignment->errors();
			}
        }
			
		$this->set(compact(array_keys($data)));	
		$this->set('errors', $errors);
		$this->set('assign', $assign);
		$this->set('elements', $this->LayerElement->setElements());
	}
	
// https://github.com/OrigamiStructures/StudioManagerScratch/issues/63 
// and issue 24
//	protected function _refreshFormatCounterCaches($form_data) {
//		
//	}
	
}

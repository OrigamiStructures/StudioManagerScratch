<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Lib\Range;
use Cake\Cache\Cache;

/**
 * Pieces Controller
 *
 * @property \App\Model\Table\PiecesTable $Pieces
 */
class PiecesController extends AppController
{
	
	public $components = ['ArtworkStack'];

// <editor-fold defaultstate="collapsed" desc="STANDARD CRUD">
	/**
	 * Index method
	 *
	 * @return void
	     */
	public function index() {
		$conditions = [];
		if ($this->SystemState->urlArgIsKnown('format')) {
			$conditions = ['Pieces.format_id' => $this->SystemState->queryArg('format')];
		} elseif ($this->SystemState->urlArgIsKnown('edition')) {
			$conditions = ['Pieces.edition_id' => $this->SystemState->queryArg('edition')];
		} elseif ($this->SystemState->urlArgIsKnown('artwork')) {
			$conditions = ['Artworks.id' => $this->SystemState->queryArg('artwork')];
		}
		$query = $this->Pieces->find('all')
				->where($conditions)
				->contain(['Users', 'Editions', 'Formats', 'Editions.Artworks']);
		$this->set('pieces', $this->paginate($query));
		$this->set('_serialize', ['pieces']);
	}

	/**
	 * View method
	 *
	 * @param string|null $id Piece id.
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	     */
	public function view($id = null)     {
		$piece = $this->Pieces->get($id,
				[
			'contain' => ['Users', 'Editions', 'Formats', 'Dispositions']
		]);
		$this->set('piece', $piece);
		$this->set('_serialize', ['piece']);
	}

	/**
	 * Add method
	 *
	 * @return void Redirects on successful add, renders view otherwise.
	     */
	public function add()     {
		$piece = $this->Pieces->newEntity();
		if ($this->request->is('post')) {
			$piece = $this->Pieces->patchEntity($piece, $this->request->data);
			if ($this->Pieces->save($piece)) {
				$this->Flash->success(__('The piece has been saved.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('The piece could not be saved. Please, try again.'));
			}
		}
		$users = $this->Pieces->Users->find('list', ['limit' => 200]);
		$editions = $this->Pieces->Editions->find('list', ['limit' => 200]);
		$formats = $this->Pieces->Formats->find('list', ['limit' => 200]);
		$this->set(compact('piece', 'users', 'editions', 'formats'));
		$this->set('_serialize', ['piece']);
	}

	/**
	 * Edit method
	 *
	 * @param string|null $id Piece id.
	 * @return void Redirects on successful edit, renders view otherwise.
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	     */
	public function edit($id = null)     {
		$piece = $this->Pieces->get($id, [
			'contain' => []
		]);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$piece = $this->Pieces->patchEntity($piece, $this->request->data);
			if ($this->Pieces->save($piece)) {
				$this->Flash->success(__('The piece has been saved.'));
				$artwork_id = $this->Pieces->Editions->get($piece->edition_id, [
					'select' => ['artwork_id']
				])->artwork_id;
				return $this->redirect(['controller' => 'artworks', 'action' => 'validate_quantities', $artwork_id]);
			} else {
				$this->Flash->error(__('The piece could not be saved. Please, try again.'));
			}
		}
		$users = $this->Pieces->Users->find('list', ['valueField' => 'username', 'limit' => 200]);
		$editions = $this->Pieces->Editions->find('list', ['valueField' => 'title', 'limit' => 200])
				->where(['Editions.id' => $piece->edition_id]);

		$formats = $this->Pieces->Formats->find('list', ['valueField' => 'title', 'limit' => 200])
				->where(['Formats.edition_id' => $piece->edition_id]);
		$this->set(compact('piece', 'users', 'editions', 'formats'));
		$this->set('_serialize', ['piece']);
	}

// </editor-fold>
	
	/**
     * Delete method
     *
     * @param string|null $id Piece id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $piece = $this->Pieces->get($id);
        if ($this->Pieces->delete($piece)) {
            $this->Flash->success(__('The piece has been deleted.'));
        } else {
            $this->Flash->error(__('The piece could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
	
	public function review() {
		
	}
	
	/**
	 * Renumber pieces in a Limited Edition
	 * 
	 * The link to arrive here is only renedered on Limited type 
	 * editions which are the only type that recieve numbers. 
	 * 
	 * Some changes might be  needed if we change rules for what can 
	 * be numbered, and a check should be made here to confirm we 
	 * hava a valid edition to work on. This check is done in the 
	 * view right now :-|
	 * 
	 * The URL query has all the relevant IDs
	 */
	public function renumber() {
		$EditionStack = $this->loadComponent('EditionStack');
		extract($EditionStack->stackQuery()); // providers, pieces
		$cache_prefix = $this->SystemState->artistId() . '-' . 
					$this->SystemState->queryArg('edition');
		
		// prevent inappropriate entry
		if (!in_array($edition->type, \App\Lib\SystemState::limitedEditionTypes())) {
			$this->Flash->set('Only numbered editions may be renumbered.');
			$this->request->referer();
		}	
		
		if ($this->request->is('post')) {

			if (isset($this->request->data['do_move'])) {
				// user confirmed accuracy of summary, try to save data
				$renumbered_pieces = Cache::read($cache_prefix . '.save_data','renumber');
				if ($this->save($renumbered_pieces)) {
					$this->Flash->set('The save was successful');
					Cache::deleteMany([
						$cache_prefix . '.summary', 
						$cache_prefix . '.save_data',
						$cache_prefix . '.request_data'],
					'renumber');
//					$this->redirect('edition review?');
				} else {
					// attempted save failed. Restore the request form data 
					// which was in a form that didn't post and so, was lost
					$this->request->data['number'] = 
						Cache::read($cache_prefix . '.request_data','renumber');
					$this->Flash->set('The save was unsuccessful');
				}
			} else {
				// user made new requests, now needs to confirm accuracy of summary
				$this->_renumber($this->request->data);
			}
		}
		
		$summary = Cache::read($cache_prefix . '.summary','renumber');
		$renumber_summary =  $summary ? $summary : FALSE;
		
		// At this point we have one of tree situations, 
		// in all cases $renumber_summary has a value.
		// 1. $renumber_summary is False, a brand new request form will render
		// 2. $renumber_summary summary is truthy, a confirmation section will render 
		// 2. An error message says the change could not be saved
		//      and the confirmation section renders again
		
		$this->set(compact(['providers', 'pieces', 'number', 'renumber_summary']));	
	}
	
	/**
	 * Prepare a summary of the request piece renumbering pattern
	 * 
	 * After the user request piece renumbering, we'll smooth out 
	 * the request and make a simple, human readible summary 
	 * for approval or rejection. Cache the message
	 * 
	 * Also, we'll either make the full save entity set or make 
	 * data that makes it easy, an we'll cache this data. If the user 
	 * approves the changes, we'll read the cache and finish up.
	 * 
	 * Finally, the request data is in a different form from the 
	 * approval. We'll have to cache that form's data so the inputs 
	 * can stay properly populated as the process continues
	 * 
	 * @param array $post_data the user's renumbering requests, this-request-data
	 */
	protected function _renumber($post_data) {
		$cache_prefix = $this->SystemState->artistId() . '-' . 
					$this->SystemState->queryArg('edition');
		
		$summary =  '<p>This will be the data to construct a summary of the requested renumbings. The user will get a button to confirm, or will use the form (still rendered below) to make further changes.</p>'
		. '<p>There will be two different buttons, one to subit an initial change request and one to confirm the changes as summarized. The controller will detect which one was pressed and act appropriately. Without javascript, this will probably require separate forms so the form data can be examinied to determine wich choice was made.</p>';
		$data = $post_data['number'];
		Cache::writeMany([
			$cache_prefix . '.summary' => $summary, 
			$cache_prefix . '.save_data' => $data,
			$cache_prefix . '.request_data' => $post_data['number']],
		'renumber');
	}
}

<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Lib\Range;
use Cake\Cache\Cache;
use App\Model\Entity\Piece;
use Cake\Collection\Collection;
use Cake\Utility\Text;

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
		$cache_prefix = $this->SystemState->artistId() . '-' . 
					$this->SystemState->queryArg('edition');
		
		$EditionStack = $this->loadComponent('EditionStack');
		extract($EditionStack->stackQuery()); // providers, pieces
		// prevent inappropriate entry
		if (!in_array($providers['edition']->type, $this->SystemState->limitedEditionTypes())) {
			$this->Flash->set('Only numbered editions may be renumbered.');
			$this->redirect($this->request->referer());
		}	
		
		if ($this->request->is('post')) {

			if (isset($this->request->data['do_move'])) {
				// user confirmed accuracy of summary, try to save data
				$renumbered_pieces = Cache::read($cache_prefix . '.save_data','renumber');
				if ($this->save($renumbered_pieces)) {
					$this->Flash->set('The save was successful');
					Cache::deleteMany([
						$cache_prefix . '.error', 
						$cache_prefix . '.summary', 
						$cache_prefix . '.save_data',
						$cache_prefix . '.request_data',
						$cache_prefix . '.fresh_entities',
						],
					'renumber');
					$this->redirect($this->request->referer());
				} else {
					// attempted save failed. Restore the request form data 
					// which was in a form that didn't post and so, was lost
					$this->request->data['number'] = 
						Cache::read($cache_prefix . '.request_data','renumber');
					$this->Flash->set('The save was unsuccessful');
				}
			} else {
				// user made new requests, now needs to confirm accuracy of summary
				$this->_renumber($this->request->data['number'], $pieces->toArray());
			}
		}
		
		$summary = Cache::read($cache_prefix . '.summary','renumber');
		$error = Cache::read($cache_prefix . '.error','renumber');
		$renumber_summary =  $summary ? $summary : FALSE;
		
		// At this point we have one of tree situations, 
		// in all cases $renumber_summary has a value.
		// 1. $renumber_summary is False, a brand new request form will render
		// 2. $renumber_summary summary is truthy, a confirmation section will render 
		// 2. An error message says the change could not be saved
		//      and the confirmation section renders again
		
		$this->set(compact(['providers', 'pieces', 'number', 'renumber_summary', 'error']));	
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
	 * @
	 * @param array $post_data the user's renumbering requests, this-request-data
	 * @param array $pieces array of all piece entities ordered by piece number
	 */
	protected function _renumber($post_data, $pieces) {
		$cache_prefix = $this->SystemState->artistId() . '-' . 
					$this->SystemState->queryArg('edition');
		$summary = [];
		$save_data = [];
		$error = FALSE;
		// all these numbered pieces should give and recieve a change
		$receive_number = $provide_number = (new Collection(array_flip(array_flip($post_data))))
				->reduce(function($accumulator, $value, $key) {
					if ($value) {
						$accumulator[$key] = $key;
						$accumulator[$value] = $value;
					}
					return $accumulator;
				}, []);
		
		
		$fresh_piece_entities = Cache::read($cache_prefix . '.fresh_entities', 'renumber');
				
		// make a streamline entity set to work on if not already done
		// make it indexed by piece number for quick access
		// this is our reference/starter data with no changes.
		if (!$fresh_piece_entities) {
			$fresh_piece_entities = (new Collection($pieces))
				->reduce(function($accumulator, $piece) {
						$accumulator[$piece->number] = new Piece([
							'id' => $piece->id,
							'number' => $piece->number,
						]);						
						return $accumulator;
				}, []);
			Cache::write($cache_prefix . '.fresh_entities', $fresh_piece_entities, 'renumber');
		}

		$second_pass = [];
		// request validation must already have happened
		foreach ($post_data as $old_number => $new_number) {
			if (!empty($new_number)) { // if a request has been made
				
				// clone and patch the entity that has a new number requested
				$save_data[$new_number] = new Piece([
							'id' => $fresh_piece_entities[$old_number]->id,
							'number' => $new_number,
						]);
				$summary[] = "Piece #$old_number becomes #$new_number";
				unset($receive_number[$old_number]);
				unset($provide_number[$new_number]);				
			}
		}
		
		$final_change = count($receive_number) + count($provide_number);
		// a fix just in case one is 0 and the other is 2, an error condition
		if ($final_change == 2 && count($receive_number) != count($provide_number)) {
			$final_change++;
		}
		switch ($final_change) {
			case 0:
				break;
			case 2:
				$new_number = array_pop($provide_number);
				$old_number = array_pop($receive_number);
				$save_data[$new_number] = new Piece([
							'id' => $fresh_piece_entities[$old_number]->id,
							'number' => $new_number,
						]);
				$summary[] = "Piece #$old_number becomes #$new_number";
				break;
			default:
				$error = ['There was a mismatch between the number of pieces that you want to renumber and the pieces who\'s numbers were reassigned to other pieces. Please resolve and continue.'];
				
				switch (count($provide_number)) {
					case 0:
						break;
					case 1:
						$error[] = 'There is no way to determine which piece should recieve number ' .
							array_pop($provide_number);
						break;
					default:
						$error[] = 'There is no way to determine where the numbers ' . 
							Text::toList($provide_number) . ' should be used.';
						break;
				}
				
				switch (count($receive_number)) {
					case 0:
						break;
					case 1:
						$error[] = 'There is no way to determine which number piece ' .
							array_pop($receive_number) . ' should recieve.';
						break;
					default:
						$error[] = 'There is no way to determine the numbers pieces ' . 
							Text::toList($receive_number) . ' should recieve.';
						break;
				}
				$error[] = 'Below are the summary below is an incomplete guess.';
				break;
		}
		Cache::writeMany([
			$cache_prefix . '.error' => $error, 
			$cache_prefix . '.summary' => $summary, 
			$cache_prefix . '.save_data' => $save_data,
			$cache_prefix . '.request_data' => $post_data],
			// fresh_entities is written earlier
		'renumber');
		osdLog($summary, 'final summary');
	}
	
}

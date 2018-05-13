<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Lib\Range;
use Cake\Cache\Cache;
use App\Model\Entity\Piece;
use Cake\Collection\Collection;
use Cake\Utility\Text;
use App\Lib\RenumberRequest;
use App\Lib\RenumberRequests;

/**
 * Pieces Controller
 *
 * @property \App\Model\Table\PiecesTable $Pieces
 */
class PiecesController extends AppController
{
	
	public $components = ['ArtworkStack'];
	
	/**
	 * Before filter
	 * 
	 * Renumbering will take at least two page visits, but we want to 
	 * eventually return to the referring page. We'll remember that here 
	 * for latter recall.
	 * 
	 * @param \Cake\Event\Event $event
	 */
	public function beforeFilter(\Cake\Event\Event $event) {
		parent::beforeFilter($event);
		if (!stristr($this->request->referer(), DS . 'renumber?')) {
			$this->SystemState->referer($this->request->referer());
		}
	}

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
	 * The URL query has all the relevant IDs
	 * beforeFilter memorized the refering page for eventual return
	 */
	public function renumber() {
		$cache_prefix = $this->_renumber_cache_prefix();
		$artwork = $this->ArtworkStack->stackQuery();
		$EditionStack = $this->loadComponent('EditionStack');
		extract($EditionStack->stackQuery()); // providers, pieces
		/* prevent inappropriate entry */
		if (!in_array($providers['edition']->type, $this->SystemState->limitedEditionTypes())) {
			$this->Flash->set('Only numbered editions may be renumbered.');
			$this->redirect($this->SystemState->referer(SYSTEM_CONSUME_REFERER));
		}	
		/* allow cacellation of renumbering process */
		if (isset($this->request->data['cancel'])) {
			$this->_clear_renumber_caches($cache_prefix);
			$this->redirect($this->SystemState->referer(SYSTEM_CONSUME_REFERER));
		}
		/*
		 * If it's not a post, we'll just render the basic form
		 * to get the users renumbering request and give a submit button
		 */
		if ($this->request->is('post') &&
				// don't know why this second test is necessary
				!isset($this->request->data['cancel'])) {
			/*
			 * If it is a post, there are two possibile TRDs because 
			 * the page can have up to two different forms. 
			 * 1. The standard request-renumbing form could have been 
			 *		submitted or resubmitted
			 * 2. An approval-of-changes form could have been submitted 
			 *		after being shown a summary of the changes proposed
			 * A third form and TRD supports the Cancel feature (above)
			 */
			if (isset($this->request->data['do_move'])) {
				/*
				 * user confirmed accuracy of summary 
				 * retreive the cached entities
				 * and try to save them
				 */
				$renumbered_pieces = Cache::read($cache_prefix . '.save_data','renumber');
				$pieceTable = $this->Pieces;
				$result = $pieceTable->connection()->transactional(
						function () use ($pieceTable, $renumbered_pieces) {
							$result = TRUE;
							foreach ($renumbered_pieces as $entity) {
								$result = $result && $pieceTable->save($entity, ['atomic' => false]);
							}
							return $result;
						});
				if ($result) {
					Cache::delete(
							"get_default_artworks[_{$this->SystemState->queryArg('artwork')}_]",
							'artwork');
					$this->Flash->set('The save was successful');
					/*
					 * On success we can clear the cached values
					 * and return to wherever we started
					 */
					$this->_clear_renumber_caches($cache_prefix);
					$this->redirect($this->SystemState->referer(SYSTEM_CONSUME_REFERER));
				} else {
					/*
					 * attempted save failed. Restore the request form data 
					 * which was in a different <Form> that didn't post
					 */
					$this->request->data['number'] = 
						Cache::read($cache_prefix . '.request_data','renumber');
					$this->Flash->set('The save was unsuccessful');
				}
			} else {
				/*
				 *  user made renumbering request, now needs to confirm accuracy of summary
				 */
				$this->_renumber($this->request->data['number'], $pieces->toArray());
			}
		} else {
			/* this is the non-posting arrival slot */
		}
		/* this is common fall-through code for all modes of request */
		if (!isset($this->request->data['number']) && 
				$request_data = Cache::read($cache_prefix . '.request_data', 'renumber')){
			$this->request->data['number'] = $request_data;
		}
		$messagePackage = Cache::read($cache_prefix . '.messagePackage','renumber');
//		$error = Cache::read($cache_prefix . '.error','renumber');
		$renumber_summary =  $messagePackage ? $messagePackage : FALSE;
		/*
		 * At this point we have one of tree situations, 
		 * in all cases $renumber_summary has a value.
		 * 1. $renumber_summary is False, a brand new request form will render
		 * 2. $renumber_summary summary is truthy, a confirmation section will render 
		 * 2. An error message says the change could not be saved
		 *      and the confirmation section renders again
		*/
		$this->set(compact(['artwork', 'providers', 'pieces', 'number', 'renumber_summary'/*, 'error'*/]));
	}
	
	/**
	 * Prepare a summary of the request piece renumbering pattern
	 * 
	 * After the user requests piece renumbering, we'll smooth out 
	 * the request and make a simple, human readible summary 
	 * for approval or rejection. Cache the message
	 * 
	 * Also, we'll make the full save entity set an we'll cache 
	 * this data. If the user approves the changes, we'll read 
	 * the cache and finish up.
	 * 
	 * Finally, the request data is in a different <Form> from the 
	 * approval. We'll have to cache that form's data so the inputs 
	 * can stay properly populated as the process continues
	 * 
	 * @param array $post_data the user's renumbering requests
	 * @param array $pieces array of all piece entities ordered by number
	 */
	protected function _renumber($post_data, $pieces) {
		$cache_prefix = $this->_renumber_cache_prefix();
		$summary = [];
		$save_data = [];
		$error = FALSE;
		
		/*
		 * We need a master set of piece entities to reference. 
		 * These will provide id and number data as recorded in 
		 * the db. The will be stored in an array and keyed by 
		 * piece->number for easy access. Once assembled, the 
		 * reference data is stored in a cache.
		 */
		$fresh_piece_entities = Cache::read($cache_prefix . '.fresh_entities', 'renumber');		
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
		/*
		 * All involved pieces must be used once as a reciever 
		 * and once as a provider. We'll assemble two lists and 
		 * remove items as they get used. The left overs will 
		 * allow completion or error reporting. 
		 * We'll also keep track of requests that are invalid piece numbers 
		 * and compress the post_data down to a useful request array. 
		 */
		$requests = new RenumberRequests(
				array_keys($post_data), 
				$this->SystemState->queryArg('edition'),
				$this->SystemState->artistId());
//		$reduction = (new Collection(array_flip(array_flip($post_data))))
//				->reduce(function($accumulator, $value, $key) use ($fresh_piece_entities) {
		$reduction = (new Collection($post_data))
			->reduce(function($accumulator, $value, $key) use ($fresh_piece_entities, $requests) {
				if ($value) {
					if (array_key_exists($value, $fresh_piece_entities)) {
						$requests->insert(new RenumberRequest( //$old, $new, $piece_id
								$key, $value, $fresh_piece_entities[$value]->id));
					} else {
						$accumulator['error'][$key] = 
								new RenumberRequest($key, $value, NULL);
						$requests->insert($accumulator['error'][$key]);
					}
				}
			}, []);
						
		if ($requests->heap()->count() === 0) {
			$this->_clear_renumber_caches($cache_prefix);
			return; 
		}
		
		/*
		 * Everything goes into a cache. Will expire in 90 min.
		 * We could return variables in some cases, but the newly 
		 * assembled entities should be kept secure here so they 
		 * can't be altered and don't need to be recreated 
		 * when the user approves the summaries
		 */
//		osd($requests->messagePackage());
//		osd($requests->heap()->count());
		Cache::writeMany([
//			$cache_prefix . '.error' => $requests->messagePackage()->errors(), // variable?
			$cache_prefix . '.messagePackage' => $requests->messagePackage(), // variable?
			$cache_prefix . '.save_data' => $save_data,
			/*
			 * post data is needed in case the user approves a save 
			 * but the save fails. The approval comes in on a form 
			 * that doesn't have this data, so it would have 
			 * been lost except for this cach
			 */
			$cache_prefix . '.request_data' => $post_data],'renumber');
			/*
			 * fresh_entities is written earlier :
			 * $cache_prefix.fresh_entities, $fresh_piece_entities
			 */
	}
	
	/**
	 * Centralized location to clear all renumbering caches for an edition
	 * 
	 * Only works in contexts where there is an 'edition' id query arg
	 */
	protected function _clear_renumber_caches() {
		$cache_prefix = $this->_renumber_cache_prefix();
		Cache::deleteMany([
//			$cache_prefix . '.error', 
			$cache_prefix . '.messagePackage', 
			$cache_prefix . '.save_data',
			$cache_prefix . '.request_data',
			$cache_prefix . '.fresh_entities',
			],
		'renumber');
	}
	
	/**
	 * Centralized generator for the renumbering cach prefix
	 * 
	 * Only works in contexts where there is an 'edition' id query arg
	 * @return string
	 */
	protected function _renumber_cache_prefix() {
		return $this->SystemState->artistId() . '-' . 
					$this->SystemState->queryArg('edition');
	}
}

<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Controller\Component\UserContextComponent;
use App\Lib\Range;
use App\Model\Entity\ArtStack;
use App\Model\Table\ArtStacksTable;
use Cake\Cache\Cache;
use App\Model\Entity\Piece;
use Cake\Collection\Collection;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use App\Lib\RenumberRequest;
use App\Lib\RenumberRequests;
use App\Lib\RenumberMessaging;
use Cake\Http\Exception\BadRequestException;
use App\Controller\Component\LayersComponent;
use App\Model\Lib\Providers;
use Cake\Utility\Hash;
use Twig\Error\RuntimeError;

/**
 * Pieces Controller
 *
 * @property \App\Model\Table\PiecesTable $Pieces
 * @property UserContextComponent $UserContext
 */
class PiecesController extends AppController
{

	public $components = ['Layers', 'UserContext'];

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
			$this->refererStack($this->request->referer());
		}
	}

// <editor-fold defaultstate="collapsed" desc="STANDARD CRUD">
	/**
	 * Index method
	 *
	 * @return void
	     */
	public function index() {
	    $formatId = Hash::get($request->getQueryParams(), 'format', null);
	    $editionId = Hash::get($request->getQueryParams(), 'edition', null);
	    $artworkId = Hash::get($request->getQueryParams(), 'artwork', null);

		$conditions = [];
		if (!is_null($formatId)) {
			$conditions = ['Pieces.format_id' => $formatId];
		} elseif (!is_null($editionId)) {
			$conditions = ['Pieces.edition_id' => $editionId];
		} elseif (!is_null($artworkId)) {
			$conditions = ['Artworks.id' => $artworkId];
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
			$piece = $this->Pieces->patchEntity($piece, $this->request->getData());
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
			$piece = $this->Pieces->patchEntity($piece, $this->request->getData());
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

// </editor-fold>

 	public function review() {

	}

	/**
	 * Renumber pieces in a Limited Edition
	 *
	 * The URL query has all the relevant IDs
	 * beforeFilter memorized the refering page for eventual return
	 * _renumer cache has evolving request data (up to 90 minutes old)
     *
     * @todo there is no verification that the user has access to the artwork
     * @todo $providers and $pieces were made by a (now) delete class.
     *      I think this method is still valuable and so it documents the
     *      old data structure so it can be repaired.
     *      Also see EditionsController::assign()
	 */
	public function renumber() {
        /*
         * This method is in transition.
         * The old EditionStackComponent::stackQuery() is now dead and
         * ArtStack has been outfitted with replacement code.
         */
        $artId = Hash::get($this->request->getQueryParams(), 'artwork');
        /* @var ArtStacksTable $ArtStackTable */
        /* @var StackSet $artworks */
        /* @var ArtStack $artStack */
        /* @var Atrwork $arworkEntity */

        $ArtStackTable = TableRegistry::getTableLocator()->get('ArtStacks');
        $artworks = $ArtStackTable->find('stacksFor', ['seed' => 'artwork', 'ids' => [$artId]]);
        $artStack = $artworks->shift();
        $artworkEntity = $artStack->artwork->shift();

        $this->contextUser()->set('artist', $artworkEntity->user_id);
        // use the new stub class for Processes
        $result = $this->UserContext->required(['artist']);

        if ($result !== true) {
            return $result;
        }

        extract($artStack->oldEditionStack('13')); // providers, pieces, artwork

        $this->set(compact('providers', 'artwork', 'pieces'));
//        osd($providers);
//        osd($artwork);
//        osd($pieces);
//        die;

//		$cache_prefix = $this->_renumber_cache_prefix();

        /* Original code resumes here */

		/* prevent inappropriate entry */
//		if (!$providers->isLimitedEdition()) {
//			$this->Flash->set('Only numbered editions may be renumbered.');
//			$this->redirect($this->refererStack(SYSTEM_CONSUME_REFERER));
//		}
//		/* allow cacellation of renumbering process */
//        $cancel = $this->request->getData('cancel');
//		if (isset($cancel)) {
//			$this->_clear_renumber_caches($cache_prefix);
//			$this->redirect($this->refererStack(SYSTEM_CONSUME_REFERER));
//		}
//		/*
//		 * If it's not a post, we'll just render the basic form
//		 * to get the users renumbering request and give a submit button
//		 */
//		if ($this->request->is('post') /*&&
//				// don't know why this second test is necessary
//				!isset($this->request->data['cancel'])*/) {
//			/*
//			 * If it is a post, there are two possibile TRDs because
//			 * the page can have up to two different forms.
//			 * 1. The standard request-renumbing form could have been
//			 *		submitted or resubmitted
//			 * 2. An approval-of-changes form could have been submitted
//			 *		after being shown a summary of the changes proposed
//			 * A third form and TRD supports the Cancel feature (above)
//			 */
//			$doMove = $this->request->getData('do_move');
//			if (isset($doMove)) {
//				/*
//				 * user confirmed accuracy of summary
//				 * retreive the cached change requests,
//				 * assemble entities, and try to save them
//				 */
//				$messagePackage = Cache::read($cache_prefix . '.messagePackage','renumber');
//				if ($messagePackage === FALSE) {
//					$this->Flash->error("Your request expired after 90 minutes. Sorry.");
//					$this->redirect($this->refererStack(SYSTEM_VOID_REFERER));
//					/*
//					 * @todo Don't know why the redirect always falls through
//					 *			so, for now this is an exception
//					 */
//					throw new BadRequestException("Your request expired after 90 minutes. Sorry.");
//				}
//				$renumbered_pieces = [];
//				foreach ($pieces as $piece) {
//					if ($change = $messagePackage->request($piece->number)) {
//						$renumbered_pieces[] = new Piece([
//							'id' => $piece->id,
//							'number' => $change->new]
//						);
//					}
//				}
//				$pieceTable = $this->Pieces; //'use' won't accept property... ?
//				$result = $pieceTable->getConnection()->transactional(
//						function () use ($pieceTable, $renumbered_pieces) {
//							$result = TRUE;
//							foreach ($renumbered_pieces as $entity) {
//								$result = $result && $pieceTable->save($entity, ['atomic' => false]);
//							}
//							return $result;
//						});
//				if ($result) {
//				    $artworkId = Hash::get($this->request->getQueryParams(), 'artwork');
//					Cache::delete("get_default_artworks[_{$artworkId}_]",'artwork');
//					$this->Flash->set('The save was successful');
//					/*
//					 * On success we can clear the cached values
//					 * and return to wherever we started
//					 */
//					$this->_clear_renumber_caches($cache_prefix);
//					$this->redirect($this->refererStack(SYSTEM_CONSUME_REFERER));
//				} else {
//					/*
//					 * attempted save failed. Restore the request form data
//					 * which was in a different <Form> that didn't post
//					 */
//					$this->request = $this->request->withData(
//					    'number', Cache::read($cache_prefix . '.request_data','renumber'));
//					$this->Flash->set('The save was unsuccessful');
//				}
//			} else {
//                /*
//                 *  user made renumbering request, now needs to confirm accuracy of summary
//                 */
//                $pieces = is_array($pieces) ? $pieces : $pieces->toArray();
//                $this->_renumber($this->request->getData('number'), $pieces);
//            }
//		}
//		/* this is common fall-through code for all modes of request */
//        $number = $this->request->getData('number');
//		if (!isset($number) &&
//				$request_data = Cache::read($cache_prefix . '.request_data', 'renumber')){
//			/*
//			 * if the user is going through the approval process, they can be
//			 * headed back to the page with form data that doesn't include
//			 * the renumber requests. We saved that data so the form can stay
//			 * populated throughout the process.
//			 */
//			$this->request = $this->request->withData('number', $request_data);
//		}
//		/**
//		 * Not sure how to autoald and the class is needed
//		 * for when the cached object arrives
//		 */
//		new RenumberMessaging([]);
//		$messagePackage = Cache::read($cache_prefix . '.messagePackage','renumber');
//		/*
//		 * At this point we have one of three situations,
//		 * in all cases $messagePackage has a value.
//		 * 1. $messagePackage is False, a brand new request form will render
//		 * 2. $messagePackage summary is truthy, a confirmation section will render
//		 * 2. An error message says the change could not be saved
//		 *      and the confirmation section renders again
//		 *
//		 * $artwork is the standard stackQuery
//		 * $providers is ['edition' => EditionEntity, 'format' => [FormatEntity, ...]
//		 * $pieces is [PieceEntity, ...] for every piece in the Edition, assigned or not
//		 * $messagePackage is a RenumberMessaging object
//		*/
////		osd($pieces->toArray(), 'pieces');
//		$this->set('elements', $this->Layers->setElements());
//		$this->set(compact(['providers', 'pieces', 'messagePackage', 'elements']));
	}

	/**
	 * Prepare a summary of the request piece renumbering pattern
	 *
	 * After the user requests piece renumbering, we'll smooth out
	 * the request and make a simple, human readible summary
	 * of changes and errors for approval or rejection. Cache the messages
	 *
	 * Finally, the request data is in a different <Form> from the
	 * approval. We'll have to cache that form's data so the inputs
	 * can stay properly populated as the while we show the summaries
	 * and wait for approval or re-submission of changes
     *
     * @todo Don't pass $post_data directly to db... is this validated
     *      or destined for a save use?
	 *
	 * @param array $post_data the user's renumbering requests
	 * @param array $pieces array of all piece entities ordered by number
	 */
	protected function _renumber($post_data, $pieces) {
		$cache_prefix = $this->_renumber_cache_prefix();

		$requests = new RenumberRequests(array_keys($post_data));
		foreach ($post_data as $old => $new) {
				if ($new) {
					$requests->insert(new RenumberRequest($old, $new));
				}
			};

		if ($requests->heap()->count() === 0) {
			$this->_clear_renumber_caches($cache_prefix);
			return;
		}

		/*
		 * Everything goes into a cache. Will expire in 90 min.
		 *
		 * post data is needed in case the user approves a save
		 * but the save fails. The approval comes in on a form
		 * that doesn't have this data, so it would have
		 * been lost except for this cach
		 */
		Cache::writeMany([
			$cache_prefix . '.messagePackage' => $requests->messagePackage(), // variable?
			$cache_prefix . '.request_data' => $post_data],'renumber');
	}

	/**
	 * Clear all renumbering caches for an edition
	 *
	 * Only works in contexts where there is an 'edition' id query arg
	 */
	protected function _clear_renumber_caches() {
		$cache_prefix = $this->_renumber_cache_prefix();
		Cache::deleteMany([
			$cache_prefix . '.messagePackage',
			$cache_prefix . '.request_data',
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
	    $editionId = Hash::get($this->request->getQueryParams(), 'edition', null);
		return $this->contextUser()->artistId() . '-' . $editionId;
	}
}

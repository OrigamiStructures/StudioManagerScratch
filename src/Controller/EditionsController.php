<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Collection\Collection;
use App\Form\AssignmentForm;
use Cake\Utility\Hash;
use Cake\View\Form\FormContext;
use App\Lib\Traits\ArtReviewTrait;
use App\Controller\Component\LayersComponent;

/**
 * Editions Controller
 *
 * @property \App\Model\Table\EditionsTable $Editions
 */
class EditionsController extends AppController {

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
    public function index() {
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
    public function view($id = null) {
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
    public function add() {
        $edition = $this->Editions->newEntity();
        if ($this->request->is('post')) {
            $edition = $this->Editions->patchEntity($edition, $this->request->getData());
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
    public function edit($id = null) {
        $edition = $this->Editions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $edition = $this->Editions->patchEntity($edition, $this->request->getData());
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
    public function delete($id = null) {
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
        $artworkId = Hash::get($this->request->getQueryParams(), 'artwork');
        $editionId = Hash::get($this->request->getQueryParams(), 'edition');
        $this->_try_flatness_redirect($artworkId, $editionId);

        $artwork = $this->ArtworkStack->stackQuery();

        $this->set('artworks', $artwork);
        $this->set('elements', $this->Layers->setElements());
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
        $this->Artworks = TableRegistry::getTableLocator()->get('Artworks');
        $artwork = $this->ArtworkStack->stackQuery();
        if ($this->request->is('post') || $this->request->is('put')) {
//			$this->ArtworkStack->addRefinementRules();

            $artwork = $this->Artworks->patchEntity($artwork, $this->request->getData(), [
                'associated' => ['Editions', 'Editions.Formats', 'Editions.Formats.Images']
            ]);
            $index = array_keys($this->request->getData('editions'))[0];
            $deletions = $this->ArtworkStack->refinePieces($artwork,
                $this->request->getData('editions.$index.id'));

            if ($this->ArtworkStack->refinementTransaction($artwork, $deletions)) {
                $this->Flash->success(__('The edition has been changed.'));
                $artworkId = Hash::get($this->request->getQueryParams(), 'artwork');
                $editionId = Hash::get($this->request->getQueryParams(), 'edition');
                $this->redirect([
                    'controller' => 'editions',
                    'action' => 'review',
                    '?' => ['artwork' => $artworkId, 'edition' => $editionId]
                ]);
            } else {
                $this->Flash->error(__('The edition could not be saved. Please, try again.'));
            }
        }

        $this->ArtworkStack->layerChoiceLists();
        $this->set('artwork', $artwork);
        $this->set('elements', $this->Layers->setElements());
        $this->render('/Artworks/review');
    }

    /**
     * Create an new Edition and a single Format for it
     *
     * The artwork will be shown as reference info on the page
     */
    public function create() {
//		osd($this->request->data, 'trd');
        $this->Artworks = TableRegistry::getTableLocator()->get('Artworks');

        $artwork = $this->ArtworkStack->stackQuery();
        if ($this->request->is('post') || $this->request->is('put')) {
            $artwork = $this->Artworks->patchEntity($artwork, $this->request->getData(), [
                'associated' => ['Editions', 'Editions.Formats', 'Editions.Pieces', 'Editions.Formats.Images']
            ]);
            $this->ArtworkStack->allocatePieces($artwork);
//			osd($artwork, 'after adding pieces'); die;
            if ($this->ArtworkStack->refinementTransaction($artwork, [])) {
                $this->redirect([
                    'controller' => 'artworks',
                    'action' => 'review',
                    '?' => [
                        'artwork' => Hash::get($this->request->getQueryParams(), 'artwork'),
                    ]
                ]);
            } else {
                $this->Flash->error(__('The edition could not be saved. Please, try again.'));
            }
        }

        $this->ArtworkStack->layerChoiceLists();
        $this->set('artwork', $artwork);
        $this->set('elements', $this->Layers->setElements());
        $this->render('/Artworks/review');
    }

    /**
     * @todo Also see PiecesController::renumber() for a similar $providers, $pieces situation
     * @throws \Exception
     */
    public function assign() {
        $this->refererStack($this->refererStack());
        $artworkId = Hash::get($this->request->getQueryParams(), 'artwork', FALSE);
        if ($artworkId === FALSE) {
            $this->Flash->error(__('No artwork was identified so no piece assignment can be done.'));
            $this->redirect($this->refererStack());
        }
        $errors = [];

        $data = $this->ArtworkStack->focusedStack();
        extract($data); // providers, pieces, artwork

        $assignment = new AssignmentForm($data['providers']);
        $assign = new FormContext($this->request, $this->request->getData());

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($assignment->execute($this->request->getData())) {
                if ($this->EditionStack->reassignPieces($assignment, $providers->providers)) {
                    $this->Flash->error(__('The reassignments were completed.'));
// https://github.com/OrigamiStructures/StudioManagerScratch/issues/63
// and
// https://github.com/OrigamiStructures/StudioManagerScratch/issues/24
                    // on success, try triggering an event that fixes all counter cache values
                    // here are to approches. The second one would need a new listener action
                    // because the one mentioned is tied into Cache behavior and its context
//					$this->_refreshFormatCounterCaches($this->request->data);
//					$this->dispatchEvent('Pieces.fluidFormatPieces');
//					die;
                    $this->redirect($this->refererStack(SYSTEM_CONSUME_REFERER));
                } else {
                    $this->Flash->error(__('There was a problem reassigning the pieces. Please try again'));
                }
            } else {
                // have use correct input errors
                $errors = $assignment->getErrors();
            }
        }
        $this->set(compact(array_keys($data)));
        $this->set('errors', $errors);
        $this->set('assign', $assign);
        $this->set('elements', $this->Layers->setElements());
        $this->render('/Artworks/review');
    }

// https://github.com/OrigamiStructures/StudioManagerScratch/issues/63
// and
// https://github.com/OrigamiStructures/StudioManagerScratch/issues/24
//
//	}
}

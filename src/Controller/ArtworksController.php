<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Table\EditionsTable;
use App\Model\Table\FormatsTable;
use App\Model\Table\PiecesTable;
use Cake\ORM\TableRegistry;

/**
 * Artworks Controller
 *
 * @property \App\Model\Table\ArtworksTable $Artworks
 */
class ArtworksController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users']
        ];
        $this->set('artworks', $this->paginate($this->Artworks));
        $this->set('_serialize', ['artworks']);
    }

    /**
     * View method
     *
     * @param string|null $id Artwork id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $artwork = $this->Artworks->get($id, [
            'contain' => ['Users', 'Editions']
        ]);
        $this->set('artwork', $artwork);
        $this->set('_serialize', ['artwork']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $artwork = $this->Artworks->newEntity();
        if ($this->request->is('post')) {
			$Editions = TableRegistry::get('Editions');
			$Formats = TableRegistry::get('Formats');
			$Pieces = TableRegistry::get('Pieces');
            $artwork = $this->Artworks->patchEntity($artwork, $this->request->data);
			
            if ($this->Artworks->save($artwork)) {
				
				$edition = $Editions->newEntity([
					'quantity' => '1', 'type' => 'unique', 
					'user_id' => '1', 'artwork_id' => $artwork->id
				]);
				$Editions->save($edition);
				
				$format = $Formats->newEntity([
					'description' => '24" x 36" oil on canvas', 
					'user_id' => '1', 'edition_id' => $edition->id
				]);
				$Formats->save($format);
				
				$piece = $Pieces->newEntity([
					'edition_id' => $edition->id, 'format_id' => $format->id, 
					'user_id' => '1', 'number' => '1', 'quantity' => '1'
				]);
				$Pieces->save($piece);
				
                $this->Flash->success(__('The artwork has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The artwork could not be saved. Please, try again.'));
            }
        }
		$this->request->data('user_id', '1');
        $users = $this->Artworks->Users->find('list', ['limit' => 200]);
        $this->set(compact('artwork', 'users'));
        $this->set('_serialize', ['artwork']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Artwork id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $artwork = $this->Artworks->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $artwork = $this->Artworks->patchEntity($artwork, $this->request->data);
            if ($this->Artworks->save($artwork)) {
                $this->Flash->success(__('The artwork has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The artwork could not be saved. Please, try again.'));
            }
        }
        $users = $this->Artworks->Users->find('list', ['limit' => 200]);
        $this->set(compact('artwork', 'users'));
        $this->set('_serialize', ['artwork']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Artwork id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $artwork = $this->Artworks->get($id);
        if ($this->Artworks->delete($artwork)) {
            $this->Flash->success(__('The artwork has been deleted.'));
        } else {
            $this->Flash->error(__('The artwork could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}

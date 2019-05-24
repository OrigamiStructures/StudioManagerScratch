<?php
namespace App\Controller;

use CakeDC\Users\Controller\AppController;
use App\Model\Lib\Layer;

/**
 * CakePHP AdministratorController
 * @author dondrake
 */
class AdministratorController extends AppController {
	
	public function index() {
		$Users = $this->getTableLocator()->get('Users');
		$users = $Users->find('list', ['valueField' => 'username']);
		$this->set(compact('users'));
	}
	
	public function userDataIntegrity() {
		if(!$this->request->is('post') || !$this->request->data('users')) {
			$this->redirect('administrator');
		}
		$user_id = $this->request->data('users');
		$Artwork = $this->getTableLocator()->get('Artworks');
		$Edition = $this->getTableLocator()->get('Editions');
		$Format = $this->getTableLocator()->get('EditionsFormats');
		$Piece = $this->getTableLocator()->get('Pieces');
		$condition = ['user_id' => $user_id];
		
		$artworks = new Layer(
				$Artwork->find('all')
				->where($condition)
				->toArray(), 
				'artworks'
			);
		$editions = new Layer(
				$Edition->find('all')
				->where($condition)
				->toArray(), 
				'editions'
			);
		$formats = new Layer(
				$Format->find('all')
				->where($condition)
				->toArray(), 
				'formats'
			);
		$pieces = new Layer(
				$Piece->find('all')
				->where($condition)
				->toArray(), 
				'pieces'
			);
		
		$this->artworks = $artworks->load();
		$this->editions = $editions->load();
		$this->formats = $formats->load();
		$this->pieces = $pieces->load();
		
		foreach ($artworks->IDs() as $artworkID) {
			osd($artworkID, 'ARTWORK');
			$this->recordArtworkUse($artworkID);
			$editionIDs = $editions
						->find()
						->specifyFilter('artwork_id', $artworkID)
						->loadValueList('id');
			osd($editionIDs, 'edition ids');
			foreach ($editionIDs as $editionID) {
				osd($editionID, 'EDITION');
				$formatIDs = $formats
						->find()
						->specifyFilter('edition_id', $editionID)
						->loadValueList('id');
				$pieceIDs = $pieces
						->find()
						->specifyFilter('edition_id', $editionID)
						->loadValueList('id');
				$pieceFormats = $pieces
						->find()
						->specifyFilter('edition_id', $editionID)
						->loadDistinct('format_id');
				$this->recordEditionUse($editionID);
				$this->recordPieceUse($pieceIDs);
				osd($formatIDs, "format ids, edition #$editionID, artwork #$artworkID");
				osd($pieceFormats, "pieces formats, edition #$editionID, artwork #$artworkID");
			}
		}
	}
	
	private function recordEditionUse($id) {
		
	}
	
	public function recordFormatUse($id) {
		
	}
	
	public function recordArtworkUse($id) {
		
	}
	
	public function recordPieceUse($id) {
		
	}
	
}

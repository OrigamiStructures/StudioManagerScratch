<?php
namespace App\Controller;

use CakeDC\Users\Controller\AppController;
use App\Model\Lib\Layer;
use App\Lib\Range;
use Cake\ORM\TableRegistry;

/**
 * CakePHP AdministratorController
 * @author dondrake
 */
class AdministratorController extends AppController {

	public function index() {
		$Users = TableRegistry::getTableLocator()->get('Users');
		$users = $Users->find('list', ['valueField' => 'username']);
		$this->set(compact('users'));
	}

	public function userDataIntegrity() {
		if(!$this->request->is('post') || is_null($this->request->getData('users'))) {
			$this->redirect('administrator');
		}
		$user_id = $this->request->getData('users');
		$Artwork = TableRegistry::getTableLocator()->get('Artworks');
		$Edition = TableRegistry::getTableLocator()->get('Editions');
		$Format = TableRegistry::getTableLocator()->get('EditionsFormats');
		$Piece = TableRegistry::getTableLocator()->get('Pieces');
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

		$this->errors = [
			'pieces' => [
				'missing format' => []
			]
		];
		$this->artworks = $artworks->load();
		$this->editions = $editions->load();
		$this->formats = $formats->load();
		$this->pieces = $pieces->load();

		osd(is_object($artworks));
		foreach ($artworks->IDs() as $artworkID) {
			$this->recordArtworkUse($artworkID);
			$editionIDs = array_keys($editions
						->find()
						->specifyFilter('artwork_id', $artworkID)
						->load());
			foreach ($editionIDs as $editionID) {
				$formatIDs = array_keys($formats
						->find()
						->specifyFilter('edition_id', $editionID)
						->load());
				$pieceSet = $pieces
						->find()
						->specifyFilter('edition_id', $editionID)
						->load(LAYERACC_LAYER);
				osd(is_object($pieceSet));
				$pieceIDs = $pieceSet->IDs();
				$pieceFormats = $pieceSet->loadDistinct('format_id');

				$this->verifyPieceFormatLink($pieceFormats, $formatIDs, $pieceSet);
				$this->recordPieceUse($pieceIDs);

				$this->recordEditionUse($editionID);
			}

		}
		osd($this->errors);
		osd(Range::arrayToString(array_keys($this->pieces)), 'Unreferenced pieces');
		osd(array_keys($this->editions), 'Unreferenced editions');
	}

	private function recordEditionUse($id) {
		unset($this->editions[$id]);
	}

	private function recordFormatUse($id) {

	}

	private function recordArtworkUse($id) {

	}

	/**
	 * Remove pieces from the master checklist
	 *
	 * @param type $id
	 */
	private function recordPieceUse($pieceIDs) {
		foreach ($pieceIDs as $pieceId) {
			unset($this->pieces[$pieceId]);
		}
	}

	/**
	 * Verifies that pieces reference only formats from the editions pool
	 *
	 * @param type $pieceFormats
	 * @param type $formatIDs
	 * @param type $pieceSet
	 */
	private function verifyPieceFormatLink($pieceFormats, $formatIDs, $pieceSet) {
		foreach ($pieceFormats as $formatId) {
			if (!in_array($formatId, $formatIDs)){
				$pieces = $pieceSet
						->find()
						->specifyFilter('format_id', $formatId)
						->load();
				$this->errors['pieces']['missing format'] =
						array_merge(
								$this->errors['pieces']['missing format'],
								layer($pieces)->IDs()
						);
			}
		}
	}



}

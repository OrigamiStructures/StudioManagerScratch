<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Locations Controller
 *
 * @property \App\Model\Table\LocationsTable $Locations
 */
class SearchController extends AppController
{

//	public function initialize() {
//		parent::initialize();
//		$this->loadComponent('ArtworkStack');
//	}

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
//		$this->refererStack($this->referer());

        $query_string = $this->request->getData('search');
		if (!is_null($query_string)) {
			$Artworks = TableRegistry::getTableLocator()->get('Artworks');
			$Members = TableRegistry::getTableLocator()->get('Members');

			$artworks = $Artworks->find('search', [$query_string]);
//			osd($art);
//			$edition = '';
//			$format = '';
			$members = $Members->find('search', [$query_string]);;
		}

		$this->set(compact('artworks', 'members'));
//		$this->redirect($this->refererStack(SYSTEM_CONSUME_REFERER));
    }

}

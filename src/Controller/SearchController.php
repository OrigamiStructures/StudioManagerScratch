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

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
//		$this->SystemState->referer($this->referer());
		
		if (!is_null($this->request->data('search'))) {
			$query_string = $this->request->data('search');
			$Artworks = TableRegistry::get('Artworks');
			$Members = TableRegistry::get('Members');
			
			$art = $Artworks->find('search', [$query_string]);
//			$edition = '';
//			$format = '';
			$members = $Members->find('search', [$query_string]);;
		}
		
		$this->set(compact('art', 'members'));
//		$this->redirect($this->SystemState->referer(SYSTEM_CONSUME_REFERER));
    }

}

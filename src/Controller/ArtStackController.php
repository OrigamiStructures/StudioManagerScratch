<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Description of ArtStackController
 *
 * @author dondrake
 */
class ArtStackController extends AppController{
	
	public function initialize() {
		if (in_array($this->request->action, ['review', 'refine', 'create', 'createUnique'])) {
			$this->viewBuilder()->className('ArtStack');
		}
		parent::initialize();
	}
	
}

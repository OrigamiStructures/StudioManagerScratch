<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ArtStackController
 * 
 * This is a parent class for Controllers that need to output standard 
 * Artwork Stack views. The SubController extends this class, then the action(s) 
 * that need to output the stack must be named in this classes intialize() 
 * method. This done, the ArtStack View class will be used.
 * 
 * That View class takes care of making the proper Helpers and what-not 
 * available to support the stack output. 
 * 
 * In truth, any controller could extend this class since, if it doesn't 
 * have an action mentioned in initialize() it will work as normal.
 *
 * @author dondrake
 */
class ArtStackController extends AppController{
	
	public function initialize() {
		if (in_array($this->request->action, 
				['review', 'refine', 'create', 'createUnique', 'assign'])) {
			$this->viewBuilder()->className('ArtStack');
		}
		parent::initialize();
	}
	
}

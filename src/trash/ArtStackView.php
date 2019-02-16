<?php
namespace App\View;

use App\View\AppView;
use App\Lib\EditionHelperFactory;


/**
 * Description of ArtStackView
 * 
 * @todo ArtStackElement is being written out of the system. 
 *		It's being replaced by LayersComponent and some other class to 
 *		handle the piece table functions (as yet undetermined)
 *
 * @author dondrake
 */
class ArtStackView extends AppView{

	public function initialize() {
		parent::initialize();
		$this->loadHelper('DispositionTools');
        $this->loadHelper('ArtElement', ['className' => 'ArtStackElement']);
		$this->EditionFactory = new EditionHelperFactory($this);
	}
	
}

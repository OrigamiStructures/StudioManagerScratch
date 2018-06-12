<?php
namespace App\View;

use App\View\AppView;
use App\Lib\EditionHelperFactory;


/**
 * Description of ArtStackView
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

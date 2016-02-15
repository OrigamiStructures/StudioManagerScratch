<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Cache\Cache;
use App\Model\Entity\Disposition;

/**
 * CakePHP DispositionManagerComponent
 * @author dondrake
 */
class DispositionManagerComponent extends Component {
	
    public function initialize(array $config) 
	{
		$this->controller = $this->_registry->getController();
		$this->SystemState = $this->controller->SystemState;
	}

	/**
	 * Read the current disposition or create a new one
	 * 
	 * @return Disposition The evolving or brand new disposition
	 */
	public function read() {
		return Cache::remember($this->SystemState->artistId(), [$this, 'generate'], 'dispo');
	}
	
	public function discard() {
		Cache::delete($this->SystemState->artistId(), 'dispo');
	}
	
	/**
	 * Make a brand new Disposition Entity
	 * 
	 * @return Disposition
	 */
	public function generate() {
		return new Disposition(['id', 'pieces', 'member', 'location']);
	}
	
	public function merge(Disposition $dispostion, array $arguemnts) {
		osd($arguemnts);
		$this->identity($arguemnts);
	}
	
	protected function identity($arguments) {
		
	}
	
}

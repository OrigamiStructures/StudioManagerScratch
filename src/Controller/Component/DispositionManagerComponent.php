<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Cache\Cache;
use App\Model\Entity\Disposition;
use Cake\ORM\TableRegistry;

/**
 * CakePHP DispositionManagerComponent
 * @author dondrake
 */
class DispositionManagerComponent extends Component {
	
	public $disposition;
	
	/**
	 * Map specific disposition labels to their underlying types
	 * 
	 * Should this be in the Table class?
	 *
	 * @var array
	 */
	protected $map = [
		DISPOSITION_TRANSFER_SALE		=> DISPOSITION_TRANSFER,	
		DISPOSITION_TRANSFER_DONATION	=> DISPOSITION_TRANSFER,	
		DISPOSITION_TRANSFER_GIFT		=> DISPOSITION_TRANSFER,
		
		DISPOSITION_LOAN_SHOW			=> DISPOSITION_LOAN,
		DISPOSITION_LOAN_CONSIGNMENT	=> DISPOSITION_LOAN,
		DISPOSITION_LOAN_PRIVATE		=> DISPOSITION_LOAN,	
		DISPOSITION_LOAN_RENTAL			=> DISPOSITION_LOAN,
		
		DISPOSITION_STORE_STORAGE		=> DISPOSITION_STORE,

		DISPOSITION_UNAVAILABLE_LOST	=> DISPOSITION_UNAVAILABLE,
		DISPOSITION_UNAVAILABLE_DAMAGED => DISPOSITION_UNAVAILABLE,
		DISPOSITION_UNAVAILABLE_STOLEN  => DISPOSITION_UNAVAILABLE,
	];


	public function initialize(array $config) 
	{
		$this->controller = $this->_registry->getController();
		$this->SystemState = $this->controller->SystemState;
	}

	/**
	 * Read the current disposition or create a new one
	 * 
	 * The existance of the object generated (or pre-existing) will tip 
	 * the system into a new state that will effect all processes until 
	 * the disposition is either submitted or abandond.
	 * 
	 * It might seem to make sense to do this in initialize() so it's always ready, 
	 * but we only want this object when the user has initiated a disposition 
	 * creation process. 
	 * 
	 * @return Disposition The evolving or brand new disposition
	 */
	public function get() {
		$this->disposition = Cache::remember($this->SystemState->artistId(), [$this, 'generate'], 'dispo');
		return $this->disposition;
	}
	
	/**
	 * 
	 */
	public function discard() {
		Cache::delete($this->SystemState->artistId(), 'dispo');
	}
	
	/**
	 * Callable to make a brand new Disposition Entity
	 * 
	 * @return Disposition
	 */
	public function generate() {
		return new Disposition(['id', 'pieces' => [], 'member', 'location']);
	}
	
	public function merge(Disposition $dispostion, array $arguments) {
//		osd($arguemnts);
		$this->_register($arguments);
		$this->_setRedirect($arguments);
//		osd($this->disposition);die;
		Cache::write($this->SystemState->artistId(), $this->disposition, 'dispo');
	}
	
	/**
	 * 
	 * @param type $arguments
	 * @return 
	 */
	protected function _register($arguments) {
		if (array_key_exists('artwork', $arguments)) {
			return $this->_registerArtwork($arguments);
		} elseif (array_key_exists('member', $arguments)) {
			return $this->_registerMember($arguments);
		}
	}
	
	protected function _registerArtwork($arguments) {
		if (isset($arguments['piece'])) {
			if (!$this->disposition->hasPiece($arguments['piece'])) {
				$this->disposition->pieces[] = $this->pieceStack($arguments['piece']);
				$this->disposition->dropFormat($arguments['format']);
			}
		} else { // presence of 'format' arg is assumed now
			if (!$this->disposition->hasFormat($arguments['format'])) {
				$this->disposition->pieces[] = $this->formatStack($arguments['format']);
			}
		}
	}
	
	public function pieceStack($piece_id) {
		$Pieces = TableRegistry::get('Pieces');
		return $Pieces->get($piece_id, [
			'contain' => 'Formats.Editions.Artworks',
		]);
	}

	public function formatStack($format_id) {
		$Pieces = TableRegistry::get('Formats');
		return $Pieces->get($format_id, [
			'contain' => 'Editions.Artworks',
		]);
	}


	protected function _registerMember($arguments) {
		
	}


	protected function _setRedirect($arguments) {
		if (array_key_exists('artwork', $arguments)) {
			$this->SystemState->referer([
				'controller' => 'artworks',
				'action' => 'review',
				'?' => $arguments]);
		}
	}
	
}

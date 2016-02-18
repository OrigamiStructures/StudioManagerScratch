<?php
namespace App\View\Helper;

use Cake\View\Helper;

/**
 * CakePHP DispositionToolsHelper
 * @author dondrake
 */
class DispositionToolsHelper extends Helper {
	
	public $helpers = ['Html'];
	
	protected $dispo_label;
	
	protected $_disposition;

	/**
	 * Should the disposition panel be displayed?
	 * 
	 * @param Entity $disposition
	 * @return boolean
	 */
	public function panel($disposition) {
		if (!$disposition || strtolower($this->request->controller) == 'dispositions') {
			return false;
		} else {
			return true;
		}
	}
	
	public function disposition() {
		if (!isset($this->_disposition)) {
			$this->_disposition = Cache::read($this->SystemState->artistId(), 'dispo');
		}
		return $this->_disposition;
		
	}
/**
	 * Call point to generate a label or resolution link for disposition participants
	 * 
	 * Once a disposition is being defined and the participants start 
	 * accumulating, they need clear identity labeling. They may also be  
	 * tentative assignemnts in which case their label also needs to be a 
	 * inclomplete or link to initiate final resolution of their status.  
	 * tJust pass the object hat needs a link/label.
	 * 
	 * @param Entity $entity
	 * @return string
	 */
	public function identity($entity) {
		$class = array_pop((explode('\\', get_class($entity))));
		$method = "_identify$class";
		
		return $this->$method($entity);
	}
	
	protected function _identifyFormat($format) {
		return $this->_identifyPiece($format);
	}
	
	protected function _identifyPiece($piece) {
		if ($piece->fullyIdentified() || strtolower($this->request->controller) == 'dispositions') {
			$label = $piece->identityLabel();
		} else { 
			$label = $this->Html->link($piece->identityLabel(), [
				'controller' => 'artworks', 
				'action' => 'review', 
				'?' => $piece->identityArguments()
			]);
		}
		return $label;
	}
	
	protected function _identifyAddress($address) {
		if (count($this->disposition()->addresses) == 1) {
			$label = $this->Html->tag('p', $address->address_line);
		} else {
			$label = $this->Html->link($address->address_line, [
				'controller' => 'dispositions', 
				'action' => 'choose_address', 
				'?' => ['address' => $address->id]
			]);
		}
		return $label;
	}
	
	protected function _identifyMember($member) {
		return $member->memberLabel('title');
	}


	/**
	 * Call point to generate assignment link for various disposition participants
	 * 
	 * Once a dispoistion is being defined, new links must appear on pages 
	 * to allow different objects to be attached to the disposition.
	 * This is the call-point to generate the links. Just pass in the object 
	 * that is a potential participant and get the link back.
	 * 
	 * @param Entity $entity
	 * @return string
	 */
	public function connect($entity) {
		$class = array_pop((explode('\\', get_class($entity))));
		$method = "_connect$class";
		
		return $this->$method($entity);
	}
	
	/**
	 * Create a link to assign this piece to the current disposition
	 * 
	 * @param Entity $piece
	 * @return string
	 */
	protected function _connectPiece($piece) {
		$SystemState = $this->_View->viewVars['SystemState'];
		$label = $this->_fromLabel();
		return $this->Html->link(
			$label , [
				'controller' => 'dispositions',
				'action' => 'refine',
				'?' => $SystemState->queryArg() + ['piece' => $piece->id]
			]);	
	}
	
	/**
	 * Create a link to assign this member to the current disposition
	 * 
	 * @param Entity $member
	 * @return string
	 */
	protected function _connectMember($member) {
		$label = $this->_toLabel($member->name);
		return $this->Html->link($label, [
			'controller' => 'dispositions',
            'action' => 'refine', 
            '?' => [
                'member' => $member->id,
                ]
            ]//, 
//            ['class' => 'button']
		);
	}
	
	/**
	 * Create a link to assign this address to the current disposition
	 * 
	 * @param Entity $address
	 * @return string
	 */
	protected function _connectAddress($address) {
		$label = $this->_toLabel($address->address1);
		return $this->Html->link($label, [
			'controller' => 'dispositions',
            'action' => 'refine', 
            '?' => [
                'address' => $address->id,
                ]
            ]//, 
//            ['class' => 'button']
		);
	}
	
	/**
	 * Create a 'to' label for a disposition link
	 * 
	 * Labels for Member and Address links
	 * 
	 * @param string $name
	 * @return string
	 */
	private function _toLabel($name) {
		if (!isset($this->dispo_label)) {
			$this->dispo_label = $this->_View->viewVars['standing_disposition']->label;
		}
		switch ($this->dispo_label) {
			case DISPOSITION_LOAN_CONSIGNMENT :
			case DISPOSITION_LOAN_PRIVATE :
			case DISPOSITION_LOAN_RENTAL :
			case DISPOSITION_TRANSFER_DONATION :
			case DISPOSITION_TRANSFER_SALE :
			case DISPOSITION_TRANSFER_GIFT :
				$label = "$this->dispo_label to $name";
				break;
			case DISPOSITION_LOAN_SHOW :
			case DISPOSITION_STORE_STORAGE :
				$label = "$this->dispo_label at $name";
				break;
			default :
				$label = "Dispose to $name";
		}
		return $label;
		/**
		define('DISPOSITION_UNAVAILABLE_LOST'	, 'Lost');
		define('DISPOSITION_UNAVAILABLE_DAMAGED', 'Damaged');
		define('DISPOSITION_UNAVAILABLE_STOLEN' , 'Stolen');
		 */
	}
	
	/**
	 * Make a label for a 'piece' link
	 * 
	 * @return string
	 */
	private function _fromLabel() {
		if (!isset($this->dispo_label)) {
			$this->dispo_label = $this->_View->viewVars['standing_disposition']->label;
		}
		switch ($this->dispo_label) {
			case DISPOSITION_LOAN_CONSIGNMENT :
			case DISPOSITION_LOAN_PRIVATE :
			case DISPOSITION_LOAN_RENTAL :
			case DISPOSITION_TRANSFER_DONATION :
			case DISPOSITION_TRANSFER_SALE :
			case DISPOSITION_TRANSFER_GIFT :
			case DISPOSITION_LOAN_SHOW :
				$label = "Add to $this->dispo_label";
				break;
			case DISPOSITION_STORE_STORAGE :
				$label = "Send to $this->dispo_label";
				break;
			case DISPOSITION_UNAVAILABLE_LOST :
			case DISPOSITION_UNAVAILABLE_DAMAGED :
			case DISPOSITION_UNAVAILABLE_STOLEN :
				$label = "Add to $this->dispo_label";
				break;
			default :
				$label = "Dispose piece";
		}
		return $label;
	}
	
}

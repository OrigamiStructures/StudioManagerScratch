<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Cache\Cache;
use App\View\Helper\Traits\ValidationErrors;
use App\Lib\SystemState;

/**
 * CakePHP DispositionToolsHelper
 * @author dondrake
 */
class DispositionToolsHelper extends Helper {
	
	use ValidationErrors;
	
	public $helpers = ['Html', 'Form', 'InlineTools'];
	
	protected $dispo_label;
	
	protected $_disposition;

	
	public function __construct(\Cake\View\View $View, array $config = array()) {
		parent::__construct($View, $config);
		$this->SystemState = $View->SystemState;
	}

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
            $review_url = [
                'controller' => 'members', 
                '?' => [
                    'member' => $address->member_id,
                ]];

            $remove_url = [
                'controller' => 'dispositions', 
                '?' => [
                    'address' => $address->id,
                ]];
            $controls = $this->InlineTools->inlineReviewDelete($review_url, $remove_url);
			$label = $this->Html->tag('p', $controls . $address->address_line);
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

	public function dispositionLabel($disposition) {
		if (!is_null($disposition->member)) {
			return $this->_toLabel($disposition->member->name);
		} else {
			return $disposition->label;
		}
	}
	
	public function eventName($disposition) {
		if (!empty($disposition->name)) {
			return $this->Html->tag('p', $disposition->name);
		} else {
			return '';
		}
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
		// This assumes we came in through some 'review/ArtworkStack' pathway
		$edition = $this->SystemState->edition;
		
		$in_disposition = $this->_pieceInDisposition($piece);
		if (!$in_disposition) {
			if (SystemState::isOpenEdition($edition->type)) {
				return $this->_connectOpenPiece($piece);
				
			} else {
				return $this->_connectLimitedPiece($piece);
			}
			
		} elseif ($this->disposition()) {
			return $this->_disconnectPiece($piece);
			
		} else {
			return 'unknown status';
		}
	}
	
	/**
	 * Create a form to assign n open edition pieces to a disposition
	 * 
	 * This is displayed with a Piece that is appropriate to the current 
	 * Dispositon type and only when the dispo in live-editing. 
	 * 
	 * @param Entity $piece
	 * @return string The form
	 */
	private function _connectOpenPiece($piece) {
		$label = $this->_fromLabel();
		return $this->_View->element('Disposition/connect_open_piece', ['label' => $label, 'piece' => $piece]);
//		$input = $this->Form->input("piece.$piece->id.quantity", ['value' => 1, 'label' => $label, 'div' => FALSE]);
//		return $input;
	}
	
	/**
	 * Create a link to assign a limited edition piece to a disposition
	 * 
	 * This is displayed with a single Piece that is appropriate to 
	 * the current Dispositon type and only when the dispo in live-editing. 
	 * 
	 * @param Entity $piece
	 * @return string The link
	 */
	private function _connectLimitedPiece($piece) {
		$label = $this->_fromLabel();
		return $this->Html->link(
			$label,
			[
				'controller' => 'dispositions',
				'action' => 'refine',
				'?' => ['piece' => $piece->id] + $this->SystemState->queryArg()
		]);
	}
	
	/**
	 * Create a link to remove a piece from a disposition
	 * 
	 * This is expected to remove both Limited and Open edition pieces. 
	 * It displays with a single piece that is in the live-edit dispo
	 * 
	 * @param type $piece
	 * @return type
	 */
	private function _disconnectPiece($piece) {
		$label = 'Remove from ' . $this->disposition()->label;
		return $this->Html->link(
			$label,
			[
				'controller' => 'dispositions',
				'action' => 'remove',
				'?' => ['piece' => $piece->id] + $this->SystemState->queryArg()
		]);
	}
	
	public function _pieceInDisposition($piece) {
		$piece_exists = FALSE;
		$index = $this->disposition()->indexOfPiece($piece->id);
		if (!($index === FALSE)) {
			$piece_exists = $this->disposition()->pieces[$index]->fullyIdentified();
		}
		return $piece_exists;
	}
	
	/**
	 * Create a link to assign this member to the current disposition
	 * 
	 * @param Entity $member
	 * @return string
	 */
	protected function _connectMember($member) {
        $disposition = $this->disposition();
        if($disposition && $this->disposition()->member->id === $member->id){
            return $this->_disconnectMember($member);
        }
        if($disposition){
            $action = 'refine';
            $label = $this->_toLabel($member->name);
        } else {
            $action = 'create';
            $label = "Create a disposition for $member->name";
        }
		return $this->Html->link($label, [
			'controller' => 'dispositions',
            'action' => $action, 
            '?' => [
                'member' => $member->id,
                ]
            ]
		);
	}
    
    protected function _disconnectMember($member) {
		$label = 'Remove from ' . $this->disposition()->label;
		return $this->Html->link(
			$label,
			[
				'controller' => 'dispositions',
				'action' => 'remove',
				'?' => ['member' => $member->id] + $this->SystemState->queryArg()
		]);
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
            $this->dispo_label = isset($this->SystemState->standing_disposition) 
                    ? $this->SystemState->standing_disposition->label
                    : 'unknown';
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
				$label = "$this->dispo_label with $name";
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
	
	public function addressLabel($disposition) {
		$multiple = count($disposition->addresses) > 1;
		if ($this->SystemState->controller === 'dispositions') {
			$text_node = $multiple ? 'Pending Addresses' : '' ;
		} else {
			$text_node = $multiple ? 'Click the address you want to keep.' : '' ;
		}
		return $text_node !== '' ? $this->Html->tag('p', $text_node) : '';
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

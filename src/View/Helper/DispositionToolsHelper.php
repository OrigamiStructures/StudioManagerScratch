<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Cache\Cache;
use App\View\Helper\Traits\ValidationErrors;
use App\Lib\EditionTypeMap;

/**
 * CakePHP DispositionToolsHelper
 * @author dondrake
 */
class DispositionToolsHelper extends Helper {

	use ValidationErrors;

	public $helpers = ['Html', 'Form', 'ArtStackTools'];

	protected $_disposition;

	public function __construct(\Cake\View\View $View, array $config = array()) {
		parent::__construct($View, $config);
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
			$this->_disposition = Cache::read($this->contextUser()->artistId(), 'dispo');
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
	    list(, $class) = namespaceSplit(explode('\\', get_class($entity)));
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
            $controls = $this->ArtStackTools->inlineReviewDelete($review_url, $remove_url);
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
			return $this->_toLabel($disposition->member->name());
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
            $class = array_pop(namespaceSplit($entity));
//            $class = array_pop((explode('\\', get_class($entity))));
            $method = "_connect$class";

            return $this->$method($entity);
	}

	/**
	 * Create a link to assign this piece to the current disposition
	 *
	 * @param Entity $piece
	 * @return string
	 */
	protected function _connectPiece($piece, $edition) {
        if(isset($piece->rejected)){
            return $this->_rejectedReason($piece);
        }

		$in_disposition = $this->_pieceInDisposition($piece);
		if (!$in_disposition) {
			if (EditionTypeMap::isUnNumbered($edition->type)) {
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
		$label = $this->fromLabel();
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
		$label = $this->fromLabel();
		return $this->Html->link(
			$label,
			[
				'controller' => 'dispositions',
				'action' => 'refine',
				'?' => ['piece' => $piece->id] + $this->getView()->getRequest()->getQueryParams()
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
				'?' => ['piece' => $piece->id] + $this->getView()->getRequest()->getQueryParams()
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
        if($disposition &&
				isset($disposition->member->id) &&
				$disposition->member->id === $member->id){
            return $this->_disconnectMember($member);
        }

        $label = $this->_toLabel($member->name());

        if($disposition){
            $action = 'refine';
        } else {
            $action = 'create';
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
				'?' => ['member' => $member->id] + $this->getView()->getRequest()->getQueryParams()
		]);
    }

	/**
	 * Create a link to assign this address to the current disposition
	 *
	 * @param Entity $address
	 * @return string
	 */
	protected function _connectAddress($address) {
		$disposition = $this->disposition();
		$disposed_address_index = $disposition->indexOfAddress($address->id);
        if($disposition && $disposed_address_index !== FALSE){
            return $this->_disconnectAddress($address);
        }

		$label = $this->_toLabel($address->addressLine);

		if ($disposition) {
			$action = 'refine';
		} else {
			$action = 'create';
		}

		if (empty($label)) {
			$link = '';
		} else {
			$link = $this->Html->link($label, [
				'controller' => 'dispositions',
				'action' => 'refine',
				'?' => [
					'address' => $address->id,
					]
				]
			);
		}
		return $link;
	}

    protected function _disconnectAddress($address) {
		$label = 'Remove from ' . $this->disposition()->label;
		return $this->Html->link(
			$label,
			[
				'controller' => 'dispositions',
				'action' => 'remove',
				'?' => ['address' => $address->id]
		]);
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
		$disposition = $this->disposition();
		$disposition_type = $disposition ? $disposition->label : 'unknown';

		switch ($disposition_type) {
			case DISPOSITION_LOAN_CONSIGNMENT :
			case DISPOSITION_LOAN_PRIVATE :
			case DISPOSITION_LOAN_RENTAL :
			case DISPOSITION_TRANSFER_DONATION :
			case DISPOSITION_TRANSFER_SALE :
			case DISPOSITION_TRANSFER_GIFT :
				$label = "$disposition_type to $name";
				break;
			case DISPOSITION_LOAN_SHOW :
			case DISPOSITION_STORE_STORAGE :
				$label = "$disposition_type with $name";
				break;
			default :
				$label = "Send work to $name";
		}
		return $label;
		/**
		define('DISPOSITION_UNAVAILABLE_LOST'	, 'Lost');
		define('DISPOSITION_UNAVAILABLE_DAMAGED', 'Damaged');
		define('DISPOSITION_UNAVAILABLE_STOLEN' , 'Stolen');
		define('DISPOSITION_NFS' , 'Not For Sale');
		 */
	}

	public function addressLabel($disposition) {
		$multiple = count($disposition->addresses) > 1;
		if ($this->getView()->getRequest()->getParams('controller') === 'dispositions') {
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
	public function fromLabel() {
		$disposition = $this->disposition();
		$disposition_type = $disposition ? $disposition->label : 'unknown';

		switch ($disposition_type) {
			case DISPOSITION_LOAN_CONSIGNMENT :
			case DISPOSITION_LOAN_PRIVATE :
			case DISPOSITION_LOAN_RENTAL :
			case DISPOSITION_TRANSFER_DONATION :
			case DISPOSITION_TRANSFER_SALE :
			case DISPOSITION_TRANSFER_GIFT :
			case DISPOSITION_LOAN_SHOW :
				$label = "Add to $disposition_type";
				break;
			case DISPOSITION_STORE_STORAGE :
				$label = "Send to $disposition_type";
				break;
			case DISPOSITION_UNAVAILABLE_LOST :
			case DISPOSITION_UNAVAILABLE_DAMAGED :
			case DISPOSITION_UNAVAILABLE_STOLEN :
			case DISPOSITION_NFS :
				$label = "Add to $disposition_type";
				break;
			default :
				$label = "Transfer piece";
		}
		return $label;
	}

	public function saveLink() {
		$disposition = $this->disposition();

		// all true = minimum requirement for saving a disposition
		$member_message = $this->_memberMessage($disposition);
		$address_message = $this->_addressMessage($disposition);
		$piece_message = $this->_pieceMessage($disposition);
		$message = trim("$member_message$address_message$piece_message");

		if ($this->getView()->getRequest()->getParams('controller') !== 'dispositions' && $message === '') {
			return $this->Html->link('Save disposition',
			['controller' => 'dispositions', 'action' => 'save'],
			['class' => 'button']);
		} else {
			return $this->Html->tag('p', $message, ['class' => 'prompt']);
		}
	}

	/**
	 * Make a prompt message for completing the 'message' part of a disposition
	 *
	 * @param Entity $disposition
	 * @return string
	 */
	protected function _memberMessage($disposition) {
		return isset($disposition->member) ? '' : 'Member still needed. ';
	}

	/**
	 * Make a prompt message for completing the 'address' part of a disposition
	 *
	 * @param Entity $disposition
	 * @return string
	 */
	protected function _addressMessage($disposition) {
		if (empty($disposition->addresses)
				|| count($disposition->addresses) > 1) {
			$prompt = 'A single address is needed. ';
//		} elseif (count($disposition->addresses) === 1
//				&& stristr(array_shift($disposition->addresses)->address_line, 'unknown')) {
//			$prompt = 'A single address is needed. ';
		} else {
			$prompt = '';
		}
		return $prompt;
	}

	/**
	 * Make a prompt message for completing the 'piece' part of a disposition
	 *
	 * @param Entity $disposition
	 * @return string
	 */
	protected function _pieceMessage($disposition) {
		$prompt = '';
		if (empty($disposition->pieces)) {
			$prompt = 'At least one piece is needed';
		}
		if ($prompt === '') {
			foreach ($disposition->pieces as $piece) {
				if (!$piece->fullyIdentified()) {
					$prompt = 'At least one piece isn\'t identified';
				}
			}
		}
		return $prompt;
	}

    protected function _rejectedReason($piece) {
        return $this->Html->tag('span', $piece->rejected, ['class' => 'rejected']);
    }

}

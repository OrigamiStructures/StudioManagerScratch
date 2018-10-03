<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\Traits\ParentEntityTrait;
use App\Model\Entity\Traits\DispositionTrait;
use App\Model\Entity\Traits\EntityDebugTrait;

/**
 * Piece Entity.
 *
 * @property int $id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property int $number
 * @property int $quantity
 * @property bool $made
 * @property int $edition_id
 * @property \App\Model\Entity\Edition $edition
 * @property int $format_id
 * @property \App\Model\Entity\Format $format
 * @property \App\Model\Entity\Disposition[] $dispositions
 */
class Piece extends Entity
{

	use ParentEntityTrait;
	use DispositionTrait;
//	use EntityDebugTrait;
	
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
	
	/**
	 * CAKE BUG CAKE BUG CAKE BUG CAKE BUG CAKE BUG CAKE BUG CAKE BUG CAKE BUG
	 * 
	 * Format -> Pieces uses two keys so that pieces will completely hook up 
	 * when new pieces are saved directly on pieces. But later when count cache 
	 * process runs and an existing edition-linked piece is move to a format, 
	 * only one key changes... but counter cache only asks for 'changed' keys. 
	 * Then it tries to merge that array with the assoc-keys array. Of course 
	 * one doesn't match two so there is a failure. 
	 * 
	 * This HACK watches for this Format process (the only one with two keys) 
	 * and makes sure we get both back. Single key cases are left alone.
	 * 
	 * @param array $properties
	 * @return type
	 */
    public function extractOriginalChanged(array $properties) {
		if (count($properties == 2)) {
			$result = parent::extractOriginalChanged($properties) + 
				parent::extractOriginal($properties);
		} else {
			$result = parent::extractOriginalChanged($properties);
		}
		return $result;
	}
	
	/**
	 * provide a key that relates Pieces back to their Format or Edition
	 * 
	 * for an unassigned piece, will yield something like 917_
	 * for assigend piece, will yield something like 917_1119
	 * 
	 * Format and Edition entities can report thier matching key() 
	 * values, allowing an index lookup system or other entity matching logic. 
	 * 
	 * @return string
	 */
	public function key() {
		return $this->_key([$this->edition_id, $this->format_id]);
	}
	
	public function _getDisplayTitle() {
		return (!is_null($this->number) ? "piece #$this->number" : "$this->quantity pieces");
		
	}
	
	/**
	 * From an inverted artwork stack, identify the tip-of-the-iceberg
	 * 
	 * A normal artwork stack begins with the Artwork and sees all the children. 
	 * An inverted stack, such as that linked to a disposition, starts art the 
	 * child and contains the specific entity chain up to the Artwork. This method 
	 * creates the unique path/name label for this piece
	 * 
	 * @return string
	 */
	public function identityLabel() {
		$label = (!is_null($this->number) ? "#$this->number" : "$this->quantity pieces");
		if (is_object($this->format)) {
			$label = "{$this->format->identityLabel()}, $label";
		}
		return $label;
	}
	
	/**
	 * Get url query arguments that identify this piece
	 * 
	 * This process will cascade up the ownership chain to include 
	 * the owners up to the Artwork
	 * 
	 * @return array
	 */
	public function identityArguments() {
		$args = ['piece' => $this->id];
		if (is_object($this->format)) {
			$args = $this->format->identityArguments() + $args;
		}
		return $args;
	}
	
	/**
	 * Change the quantity by some value
	 * 
	 * $quantity can be a positive or negative
	 * 
	 * @param integer $quantity
	 * @param boolean $constrain Constrain results to >= 0
	 */
	public function increase($quantity, $constrain = FALSE) {
		$this->quantity = $this->quantity + $quantity;
		if ($constrain && $this->quantity < 0) {
			$this->quantity = 0;
		}
	}
	
	/**
	 * Getter for dispositions insures they're always available
	 * 
	 * Just the act of looking at dispositions sets them if they don't exist
	 * 
	 * 
	 * @return array The dispositions for this piece
	 */
	public function _getDispositions() {
		if (!isset($this->_properties['dispositions'])) {
			$Pieces = \Cake\ORM\TableRegistry::get('Pieces');
			$existing_dispositions = $Pieces->get($this->id, ['contain' => ['Dispositions']]);
			$this->_properties['dispositions'] = $existing_dispositions->dispositions;
		}
		return $this->_properties['dispositions'];
	}
	
	/**
	 * Is the piece assigned to a format
	 * 
	 * @return boolean
	 */
	public function isAssigned() {
		return !is_null($this->_properties['format_id']);
	}
	
	/**
	 * Is the piece collected or slated for collection in the future
	 * 
	 * @return boolean
	 */
	public function isCollected() {
		return (boolean) $this->_properties['collected'];
	}
	
	/**
	 * Is the piece free of dispositions
	 * 
	 * @return boolean
	 */
	public function isFluid() {
		return (boolean) !$this->_properties['disposition_count'];
	}
}

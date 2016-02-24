<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\Traits\ParentEntityTrait;
use App\Model\Entity\Traits\AssignmentTrait;

/**
 * Edition Entity.
 *
 * @property int $id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property string $title
 * @property string $type
 * @property int $quantity
 * @property int $artwork_id
 * @property \App\Model\Entity\Artwork $artwork
 * @property int $series_id
 * @property \App\Model\Entity\Format[] $formats
 * @property \App\Model\Entity\Piece[] $pieces
 */
class Edition extends Entity
{
	
	use ParentEntityTrait;
	use AssignmentTrait;
	
	protected $_collected;

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
	 * Fully detailed descriptive label for the edition
	 * 
	 * @return string
	 */
	public function _getDisplayTitle() {
		$type = strtolower($this->_properties['type']) === 'unique' 
				? 'Unique Work' 
				: ucwords($this->_properties['type']) . " ({$this->_properties['quantity']})";
		$title = empty($this->_properties['title']) ? ucwords($this->_properties['title']) : ucwords("{$this->_properties['title']}, ");
		return  $title . $type;
	}
	
	/**
	 * provide a key that relates Pieces back to their Format or Edition
	 * 
	 * @return type
	 */
	public function key() {
		return $this->_key([$this->id, '']);
	}
	
	/**
	 * Does the edition have any pieces that have not been assigned to a format
	 * 
	 * @return boolean
	 */
	public function hasUnassigned() {
		return $this->unassigned_piece_count > 0;
	}
	
	/**
	 * Count of pieces that have not been assigned to formats
	 * 
	 * unassigned_piece_count is edition size - assigned_piece_count
	 * 
	 * @return integer
	 */
	public function _getUnassignedPieceCount() {
		return $this->_properties['quantity'] - $this->_properties['assigned_piece_count'];
	}
	
	/**
	 * Does the edtion have pieces that have dispositions
	 * 
	 * @return boolean
	 */
	public function hasDisposed() {
		return $this->disposed_piece_count > 0;
	}
	
	/**
	 * The number of pieces in the edition that have dispositions
	 * 
	 * disposed_piece_count is calculated as assigned_piece_count - fluid_piece_count
	 * 
	 * @return integer
	 */
	public function _getDisposedPieceCount() {
		return $this->_properties['assigned_piece_count'] - $this->_properties['fluid_piece_count'];
	}
	
	/**
	 * Does the edition have any pieces that can be sold
	 * 
	 * @return boolean
	 */
	public function hasSalable() {
		return $this->salable_piece_count > 0;
	}
	
	/**
	 * Does the edition have any pieces with dispositions categorized as 'collected'
	 * 
	 * @return boolean
	 */
	public function hasCollected() {
		return $this->collected_piece_count > 0;
	}
	
	/**
	 * Count of pieces that might be avaialble for sale
	 * 
	 * salable_piece_count is the edition size - collected_piece_count
	 * 
	 * @return integer
	 */
	public function _getSalablePieceCount() {
		return $this->quantity - $this->collected_piece_count;
	}
	
	public function _getUndisposedPieceCount() {
		return $this->quantity - $this->disposed_piece_count;
	}
	
	/**
	 * Count of pieces that have dispositons categorized as 'collected'
	 * 
	 * collected_piece_count is calculated directly from the contained format 
	 * entities by summing the formats 'collected' CounterCache value. 
	 * To avoid multiple 'reduce' calls, the result is cached in a property
	 * 
	 * @return integer
	 */
	public function _getCollectedPieceCount() {
		if (!isset($this->collected)) {
			$fluid_piece_count = $this->_properties['fluid_piece_count'];
			$formats = new \Cake\Collection\Collection($this->_properties['formats']);
			$this->collected = $formats->reduce(function($accumulator, $format) use ($fluid_piece_count) {
						return $accumulator + $format->collected_piece_count;
					}, 0);
		}
		return $this->collected;
	}

	/**
	 * Does the edition have pieces that are assigned but not disposed?
	 * 
	 * fluid_piece_count is a direct CounterCache value on bothe editions 
	 * and formats. On editons the where clause is:
	 * <pre>
	 *	[
	 *		'edition_id' => $piece->edition_id,
	 *		'format_id IS NOT NULL',
	 *		'disposition_count' => 0,
	 *	]
	 * </pre>
	 * 
	 * @return boolean
	 */
	public function hasFluid() {
		return $this->_properties['fluid_piece_count'] > 0;
	}
	
	/**
	 * From an inverted stack, identify the tip-of-the-iceberg
	 * 
	 * A normal artwork stack begins with the Artwork and sees all the children. 
	 * An inverted stack, such as that linked to a disposition, starts art the 
	 * child and contains the specific entity chain up to the Artwork. This method 
	 * adds to the label for the child piece
	 * 
	 * @return string
	 */
	public function identityLabel() {
		$type = strtolower($this->_properties['type']) === 'unique' 
				? 'Unique Work' 
				: ucwords($this->_properties['type']);
		$label = empty($this->_properties['title']) ? $type : ucwords($this->_properties['title']);
		if (is_object($this->artwork)) {
			$label = "{$this->artwork->identityLabel()}, $label";
		}
		return $label;
	}
	public function identityArguments() {
		return ['edition' => $this->id, 'artwork' => $this->artwork_id];
	}
	
	public function isFlat() {
		return $this->format_count === 1;
	}
}

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\Traits\ParentEntity;

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
	
	use Traits\ParentEntityTrait;

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
	
	public function hasUnassigned() {
		return $this->unassigned_piece_count > 0;
	}
	
	public function _getUnassignedPieceCount() {
		return $this->_properties['quantity'] - $this->_properties['assigned_piece_count'];
	}
	
//	public function hasAvailablePieces() {
//		return (boolean) $this_properties['quantity'] - $this->_properties['assigned_piece_count'] > 0;
//	}
	
	public function hasFluid() {
		return $this->_properties['fluid_piece_count'] > 0;
	}
	
}

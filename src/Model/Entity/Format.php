<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Entity\Traits\ParentEntity;

/**
 * Format Entity.
 *
 * @property int $id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property string $title
 * @property string $description
 * @property int $range_flag
 * @property int $range_start
 * @property int $range_end
 * @property int $image_id
 * @property int $edition_id
 * @property \App\Model\Entity\Edition $edition
 * @property int $subscription_id
 * @property \App\Model\Entity\Piece[] $pieces
 */
class Format extends Entity
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
	
	protected $_salable;

	/**
	 * Fully detailed descriptive label for the edition
	 * 
	 * @return string
	 */
	public function _getDisplayTitle() {
		$title = empty($this->_properties['title']) ? $this->_properties['description'] : $this->_properties['title'];
		return "$title";
	}
	
	public function hasAssigned() {
		return $this->_properties['assigned_piece_count'] > 0;
	}
	
	public function hasCollected() {
		return $this->_properties['collected_piece_count'] > 0;
	}
	
	public function hasSalable($undisposed) {
		return $this->salable_piece_count($undisposed) > 0;
	}
	
	public function salable_piece_count($undisposed) {
		if (!isset($this->_salable)) {
			$this->_salable = $this->disposed_piece_count - 
					$this->_properties['collected_piece_count'] + 
					$undisposed;
		}
		return $this->_salable;
	}

	public function hasFluid() {
		return $this->_properties['fluid_piece_count'] > 0;
	}
	
	public function hasDisposed() {
		return $this->_properties['assigned_piece_count'] - $this->_properties['fluid_piece_count'];
	}
	
	public function _getDisposedPieceCount() {
		return $this->_properties['assigned_piece_count'] - $this->_properties['fluid_piece_count'];
	}
	
}

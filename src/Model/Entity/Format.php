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
	
	/**
	 * Fully detailed descriptive label for the edition
	 * 
	 * @return string
	 */
	public function _getDisplayTitle() {
		$title = empty($this->_properties['type']) ? $this->_properties['description'] : $this->_properties['type'];
		return "$title";
	}
	
}

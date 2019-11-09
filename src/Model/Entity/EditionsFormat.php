<?php
namespace App\Model\Entity;

use App\Model\Entity\Traits\ParentEntityTrait;
use Cake\ORM\Entity;

/**
 * EditionsFormat Entity
 *
 * @property int $id
 * @property string $user_id
 * @property int $format_id
 * @property int $edition_id
 * @property int $assigned_piece_count
 * @property int $fluid_piece_count
 * @property int $collected_piece_count
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Format $format
 * @property \App\Model\Entity\Edition $edition
 */
class EditionsFormat extends Entity
{

    use ParentEntityTrait;

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
        'user_id' => true,
        'format_id' => true,
        'edition_id' => true,
        'assigned_piece_count' => true,
        'fluid_piece_count' => true,
        'collected_piece_count' => true,
        'user' => true,
        'format' => true,
        'edition' => true
    ];

	/**
	protected $_salable;

	/**
	 * Fully detailed descriptive label for the edition
	 *
	 * @return string
	 */
	public function _getDisplayTitle() {
		$title = empty($this->title) ? 'Format: ' . $this->description : $this->title;
		return $title;
	}

    /**
     * provide a key that relates Pieces back to their Format or Edition
     *
     * will yeild something like 917_1119
     * Piece->key() generates it's ancestory key that will match this value
     *
     * @return string
     */
    public function key() {
        return $this->_key([$this->edition_id, $this->id]);
    }

}

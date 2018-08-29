<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Collection\Collection;
use App\Model\Entity\Traits\ParentEntityTrait;
use App\Model\Entity\Traits\EntityDebugTrait;

/**
 * Disposition Entity.
 *
 * @property int $id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property string $user_id
 * @property \App\Model\Entity\User $user
 * @property int $member_id
 * @property \App\Model\Entity\Member $member
 * @property int $address_id
 * @property \App\Model\Entity\Address $address
 * @property \Cake\I18n\Time $start_date
 * @property \Cake\I18n\Time $end_date
 * @property string $type
 * @property string $label
 * @property bool $complete
 * @property int $disposition_id
 * @property string $name
 * @property \App\Model\Entity\Piece[] $pieces
 */
class Disposition extends Entity
{

	use ParentEntityTrait;
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
	 * Is this piece already in the list of pieces for this disposition?
	 * 
	 * @param type $piece_id
	 * @return boolean
	 */
//	public function hasPiece($piece_id) {
//		if (is_array($this->pieces)) {
//			$candidates = new Collection($this->pieces);
//			$existing_piece = $candidates->filter(function($piece) use ($piece_id) {
//					return $piece->fullyIdentified() && $piece->id == $piece_id;
//				});
//			return iterator_count($existing_piece) > 0;
//		}
//		return FALSE;
//	}
	
	/**
	 * Is there a Format in the Pieces list that has this id?
	 * 
	 * 	 * a Format will be in the list if the artist indicated they wanted a disposition 
	 * for some Piece in the Format but hadn't yet indicated which Pieces
	 * 
@param integer $format_id
	 * @return boolean
	 */
//	public function hasFormat($format_id) {
//		if (is_array($this->pieces)) {
//			$candidates = new Collection($this->pieces);
//			$existing_format = $candidates->filter(function($piece) use ($format_id) {
//					return !$piece->fullyIdentified() && $piece->id == $format_id;
//				});
//			return iterator_count($existing_format) > 0;
//		}
//		return FALSE;
//	}
	
	/**
	 * Remove a Format from the piece list
	 * 
	 * a Format will be in the list if the artist indicated they wanted a disposition 
	 * for some Piece in the Format but hadn't yet indicated which Pieces
	 * 
	 * @param integer $format_id
	 */
	public function dropFormat($format_id) {
		$candidates = new Collection($this->pieces);
		$surviving_pieces = $candidates->reject(function($piece) use ($format_id) {
			return !$piece->fullyIdentified() && $piece->id == $format_id;
		});
		$this->pieces = $surviving_pieces->toArray();
}
	
	public function missingDates() {
		return (empty($this->start_date) || empty($this->end_date));
	}

	public function _getMemberName() {
		return $this->first_name . ' ' . $this->last_name;
	}
}

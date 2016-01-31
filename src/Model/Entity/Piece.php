<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

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
	 * CAKE BUG CAKE BUG
	 * 
	 * Format -> Pieces uses two keys so that pieces will completely hook up 
	 * when new pieces are saved directly on pieces. But later when count cache 
	 * process runs and an existing edition-linked piece is move to a format, 
	 * only on key changes... but counter cache only asks for 'changed' keys. 
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
}

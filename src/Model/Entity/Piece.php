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
}

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

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

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity.
 *
 * @property int $id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property int $member_id
 * @property string $username
 * @property string $password
 * @property \App\Model\Entity\Member[] $members
 * @property \App\Model\Entity\Artwork[] $artworks
 * @property \App\Model\Entity\Disposition[] $dispositions
 * @property \App\Model\Entity\Edition[] $editions
 * @property \App\Model\Entity\Format[] $formats
 * @property \App\Model\Entity\Group[] $groups
 * @property \App\Model\Entity\GroupsMember[] $groups_members
 * @property \App\Model\Entity\Location[] $locations
 * @property \App\Model\Entity\Piece[] $pieces
 */
class User extends Entity
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

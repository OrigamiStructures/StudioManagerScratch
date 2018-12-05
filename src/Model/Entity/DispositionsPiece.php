<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DispositionsPiece Entity
 *
 * @property int $id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property string $user_id
 * @property int $disposition_id
 * @property int $piece_id
 * @property bool $complete
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Disposition $disposition
 * @property \App\Model\Entity\Piece $piece
 */
class DispositionsPiece extends Entity
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
        'created' => true,
        'modified' => true,
        'user_id' => true,
        'disposition_id' => true,
        'piece_id' => true,
        'complete' => true,
        'user' => true,
        'disposition' => true,
        'piece' => true
    ];
}

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Permission Entity
 *
 * @property int $id
 * @property string $layer_name
 * @property int $layer_id
 * @property string $user_id
 * @property int $manifest_id
 * @property int $edit
 *
 * @property \App\Model\Entity\Layer $layer
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Manifest $manifest
 */
class Permission extends Entity
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
        'layer_name' => true,
        'layer_id' => true,
        'user_id' => true,
        'manifest_id' => true,
        'edit' => true,
        'layer' => true,
        'user' => true,
        'manifest' => true
    ];
}

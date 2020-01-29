<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Share Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $supervisor_id
 * @property int|null $manager_id
 * @property int|null $category_id
 *
 * @property \App\Model\Entity\Supervisor $supervisor
 * @property \App\Model\Entity\Manager $manager
 * @property \App\Model\Entity\Category $category
 */
class Share extends Entity
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
        'supervisor_id' => true,
        'manager_id' => true,
        'category_id' => true,
        'user_id' => true,
        'supervisor' => true,
        'manager' => true,
        'category' => true
    ];
}

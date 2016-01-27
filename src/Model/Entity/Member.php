<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Member Entity.
 *
 * @property int $id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property string $name
 * @property int $user_id
 * @property int $image_id
 * @property \App\Model\Entity\User[] $users
 * @property \App\Model\Entity\Disposition[] $dispositions
 * @property \App\Model\Entity\Location[] $locations
 * @property \App\Model\Entity\Group[] $groups
 */
class Member extends Entity
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
    
    public function _getName(){
        switch ($this->_properties['member_type']) {
            case MEMBER_TYPE_PERSON:
                return $this->_properties['first_name'] . ' ' . $this->_properties['last_name'];
                break;

            default:
                return $this->_properties['first_name'];
                break;
        }
    }
    
    public function _getSortName(){
        switch ($this->_properties['member_type']) {
            case MEMBER_TYPE_PERSON:
                return $this->_properties['last_name'] . ', ' . $this->_properties['first_name'];
                break;

            default:
                return $this->_properties['first_name'];
                break;
        }
    }
}

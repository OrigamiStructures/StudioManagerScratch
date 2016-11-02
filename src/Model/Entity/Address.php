<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Address Entity.
 *
 * @property int $id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property string $user_id
 * @property \App\Model\Entity\User $user
 * @property int $member_id
 * @property \App\Model\Entity\Member $member
 * @property string $address1
 * @property string $address2
 * @property string $address3
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 */
class Address extends Entity
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
	
	public function _getAddressLine() {
		$address = trim("$this->address1, $this->city, $this->state $this->zip", ' ,');
		return empty($address) ? 'Address unknown' : $address;
	}
}

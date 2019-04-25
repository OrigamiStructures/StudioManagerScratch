<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Artist Entity
 *
 * @property int $id
 * @property int $member_id
 * @property string $user_id
 * @property int $member_user_id
 *
 * @property \App\Model\Entity\Member $member
 * @property \App\Model\Entity\User[] $users
 * @property \App\Model\Entity\MemberUser $member_user
 */
class Manifest extends Entity
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
        'member_id' => true,
        'user_id' => true,
        'member_user_id' => true,
        'member' => true,
        'users' => true,
        'member_user' => true
    ];
	
	/**
	 * Does the member/artist belong to the user
	 * 
	 * Users may freely create and edit artists and their works. 
	 * However, if the member record belongs to another user, then 
	 * there are some restrictions on this user's rights
	 * 
	 * @return boolean
	 */
	public function self() {
		return $this->member_user_id === $this->user_id;
	}
}

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Manifest Entity
 *
 * @property int $id
 * @property int $member_id
 * @property string $user_id
 * @property string $manager_id
 * @property bool $publish_manager
 * @property bool $publish_manager_contact
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
		'member_user_id' => true,
        'user_id' => true,
        'manager_id' => true,
        'publish_manager' => true,
        'publish_manager_contact' => true,
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
	public function selfAssigned() {
		return $this->manager_id === $this->supervisor_id;
	}
	
	public function supervisorId() {
		return $this->supervisor_id;
	}
	
	public function managerId() {
		return $this->manager_id;
	}
	
	public function artistId() {
		return $this->member_id;
	}
	
}

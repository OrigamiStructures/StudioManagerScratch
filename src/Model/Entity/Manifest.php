<?php
namespace App\Model\Entity;

use App\Model\Lib\ActorParamValidator;
use Cake\Error\Debugger;
use Cake\Log\Log;
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

    use ActorParamValidator;

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

    public function getOwnerId($actor)
    {
        $validActor = $this->validateActor($actor);

        switch ($validActor) {
            case 'artist':
                return $this->supervisor_id;
                break;
            case 'manager':
                return $this->manager_id;
                break;
            case 'supervisor':
                return $this->supervisor_id;
                break;
        }
    }
    public function getMemberId($actor)
    {
        $validActor = $this->validateActor($actor);

        switch ($validActor) {
            case 'artist':
                return $this->member_id;
                break;
            case 'manager':
                return $this->manager_member;
                break;
            case 'supervisor':
                return $this->supervisor_member;
                break;
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasSupervisor($id)
    {
        return $this->getOwnerId('supervisor') === $id;
	}

    /**
     * @param $id
     * @return bool
     */
    public function hasManager($id)
    {
        return $this->getOwnerId('manager') === $id;
	}

    /**
     * @param $id
     * @return bool
     */
    public function hasArtist($id)
    {
        return $this->getOwnerId('artist') === $id;
	}

    public function getName($actor)
    {
        $validActor = $this->validateActor($actor);

        if (!isset($this->names)) {
            $trace = var_export(Debugger::trace(), true);
            Log::write(LOG_WARNING, "Manifest::getName($actor) was called but the Manifest
            has not been configured as a link-layer for a stack." . PHP_EOL . "The trace to this getName call: " .
            PHP_EOL . $trace);
            return null;
        }

        switch ($validActor) {
            case 'supervisor':
                $index = $this->getMemberId('supervisor');
                break;
            case 'manager':
                $index = $this->getMemberId(('manager'));
                break;
            case 'artist':
                $index = $this->getMemberId('artist');
                break;
        }
        return $this->names[$index];
    }
}

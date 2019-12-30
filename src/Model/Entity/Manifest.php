<?php
namespace App\Model\Entity;

use App\Model\Lib\ActorParamValidator;
use Cake\Error\Debugger;
use Cake\Log\Log;
use Cake\ORM\Entity;

/**
 * Manifest Entity
 *
 *
 *
 * @property int $id
 * @property int $user_id
 * @property int $supervisor_id
 * @property string $supervisor_member
 * @property string $manager_id
 * @property bool $manager_member
 * @property bool $member_id
 * @property bool $c_udd
 *
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
        'supervisor_id' => true,
		'supervisor_member' => true,
        'manager_id' => true,
        'manager_member' => true,
        'member_id' => true,
        'c_udd' => true,
    ];

	/**
	 * Is the supervisor the same as the manager?
	 *
	 * Users may freely create and edit artists and their works.
	 * However, if the member record belongs to another user, then
	 * there are some restrictions on this user's rights
	 *
	 * @return boolean
	 */
	public function isSelfAssigned() {
		return $this->manager_id === $this->supervisor_id;
	}

    /**
     * Get the user id (record owner id) of the specified actor
     *
     * @param $actor string a value in ActorParamValidator::validNames
     * @return int
     * @throws \BadMethodCallException
     */
    public function getOwnerId($actor)
    {
        $validActor = $this->validateActor($actor);

        switch ($validActor) {
            case 'artist':
                $result = $this->supervisor_id;
                break;
            case 'manager':
                $result = $this->manager_id;
                break;
            case 'supervisor':
                $result = $this->supervisor_id;
                break;
        }
        return $result;
    }

    /**
     * Get the member id of the specified actor
     *
     * @param $actor string a value in ActorParamValidator::validNames
     * @return int
     * @throws \BadMethodCallException
     */
    public function getMemberId($actor)
    {
        $validActor = $this->validateActor($actor);

        switch ($validActor) {
            case 'artist':
                $result = $this->member_id;
                break;
            case 'manager':
                $result = $this->manager_member;
                break;
            case 'supervisor':
                $result = $this->supervisor_member;
                break;
        }
        return $result;
    }

    /**
     * is this the id of the supervisor record
     *
     * @param $id string
     * @return bool
     */
    public function hasSupervisor($id)
    {
        return $this->getMemberId('supervisor') === $id;
	}

    /**
     * Is this the id of the manager artist record
     *
     * @param $id string
     * @return bool
     */
    public function hasManager($id)
    {
        return $this->getMemberId('manager') === $id;
	}

    /**
     * Is this this id of the artist member record?
     *
     * @param $id string
     * @return bool
     */
    public function hasArtist($id)
    {
        return $this->getMemberId('artist') === $id;
	}

    /**
     * If $this::names has been set, return the name of the actor
     *
     * $this::names will be set when Manifests is used in PersonCards
     *
     * @param $actor string a value in ActorParamValidator::validNames
     * @return string|null
     */
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

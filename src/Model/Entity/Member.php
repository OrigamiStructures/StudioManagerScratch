<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

define('FIRST_LAST', 'FL');
define('LAST_FIRST', 'LF');
define('LABELED', 'LFL');
define('GROUP', 'Category');

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

	/**
	 * The assembled `name` property; first/last format
	 *
	 * @return string
	 */
    protected function _name(){
        switch ($this->type()) {
            case MEMBER_TYPE_PERSON:
                $name = implode(' ', [
					$this->firstName(),
					$this->lastName()
				]);
                break;
            default:
				$name = $this->_institutionName();
                break;
        }
		return trim($name);
    }

	/**
	 * The assembled `reverse name`; last/first format
	 */
    protected function _reverseName(){
        switch ($this->type()) {
            case MEMBER_TYPE_PERSON:
                $name = implode(', ', [
					$this->lastName(),
					$this->firstName(),
				]);
                break;
            default:
				$name = $this->_institutionName();
                break;
        }
		return $name;
    }

	/**
	 * Returns an institution/group name
	 *
	 * Handles messy usage of the first/last name fields for these
	 * names that only need one field
	 *
	 * @todo The Member UX needs to route Organization/Group names to last_name
	 *
	 * @return string
	 */
	protected function _institutionName() {
//		if ($this->_properties['first_name'] === $this->_properties['last_name'] ||
//				isset($this->_properties['first_name'])) {
//			$name = $this->_properties['first_name'];
//		} else {
			return $this->lastName();
//		}
//		return $name;
	}

		/**
	 * Flexible name getter
	 *
	 * Can return 3 variations of concatention
	 *	FIRST_LAST = first_name last_name
	 *  LAST_FIRST = last_name, first_name
	 *  LABELED = memeber_type: first_name last_name
	 *
	 * @param string $format
	 * @return string
	 */
	public function name($format = FIRST_LAST) {
		switch ($format) {
			case FIRST_LAST:
				return $this->_name();
				break;
			case LAST_FIRST:
				return $this->_reverseName();
				break;
			case LABELED:
				return "{$this->type()}: {$this->_name()}";
				break;
			default:
				return $this->_name();
				break;
		}
	}

	public function imageId() {
		return $this->image_id;
	}

	/**
	 * Has this Memeber collected artwork
	 *
	 * @return boolean
	 */
	public function isCollector() {
		if (!is_null($this->collector()) && $this->collector() > 0 ) {
			return TRUE;
		}
		return FALSE;
	}

	public function collector() {
		return $this->collector;
	}

	public function collectedCount() {
		$count = $this->collector();
		if (!is_null($count) && $count > 0 ) {
			return $count;
		}
		return 0;
	}

	public function isDispositionParticipant() {
		if (!is_null($this->dispositionCount()) && $this->dispositionCount() > 0 ) {
			return TRUE;
		}
		return FALSE;
	}

	public function dispositionCount() {
		$count = $this->disposition_count;
		if (!is_null($count) && $count > 0 ) {
			return $count;
		}
		return 0;
	}

	public function isActive() {
		return $this->active;
	}

	public function firstName() {
		return $this->first_name;
	}

	public function lastName() {
		return $this->last_name;
	}

	public function type() {
		return $this->member_type;
	}

	public function isCategory() {
	    return $this->type() === MEMBER_TYPE_CATEGORY;
    }

    public function isOrganization() {
        return $this->type() === MEMBER_TYPE_ORGANIZATION;
    }

    public function isPerson() {
        return $this->type() === MEMBER_TYPE_PERSON;
    }

    public function registeredUserId()
    {
        return $this->user_id;
    }
}

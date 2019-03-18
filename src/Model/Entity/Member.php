<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

define('FIRST_LAST', 'FL');
define('LAST_FIRST', 'LF');
define('LABELED', 'LFL');

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
    public function _getName(){
        switch ($this->getType()) {
            case MEMBER_TYPE_PERSON:
                $name = implode(' ', [
					$this->getFirstName(), 
					$this->getLastName()
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
    public function _getReverseName(){
        switch ($this->getType()) {
            case MEMBER_TYPE_PERSON:
                $name = implode(', ', [
					$this->getLastName(), 
					$this->getFirstName(),
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
	 * @todo The Member UX needs to route Institution/Group names to first_name
	 * 
	 * @return string
	 */
	protected function _institutionName() {
		if ($this->_properties['first_name'] === $this->_properties['last_name'] ||
				isset($this->_properties['first_name'])) {
			$name = $this->_properties['first_name'];
		} else {
			$name = $this->_properties['last_name'];
		}
		return $name;
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
				return $this->name;
				break;
			case LAST_FIRST:
				return $this->reverseName;
				break;
			case LABELED:
				return "{$this->getType()}: $this->name";
				break;
			default:
				return $this->name;
				break;
		}
	}
	
	/**
	 * Has this Memeber collected artwork
	 * 
	 * @return boolean
	 */
	public function isCollector() {
		if (!is_null($this->getCollector()) && $this->getCollector() > 0 ) {
			return TRUE;
		}
		return FALSE;
	}
	
	protected function getCollector() {
		return $this->collector;
	}
    
	public function getCollectedCount() {
		$count = $this->getCollector();
		if (!is_null($count) && $count > 0 ) {
			return $count;
		}
		return 0;
	}
	
	public function isDispositionParticipant() {
		if (!is_null($this->getDispositionCount()) && $this->getDispositionCount() > 0 ) {
			return TRUE;
		}
		return FALSE;
	}
	
	public function getDispositionCount() {
		$count = $this->disposition_count;
		if (!is_null($count) && $count > 0 ) {
			return $count;
		}
		return 0;
	}
	
	public function isActive() {
		if($this->active === 1) {
			return TRUE;
		}
		return FALSE;
	}
	
	public function getFirstName() {
		return $this->first_name;
	}
	
	public function getLastName() {
		return $this->last_name;
	}
	
	public function getType() {
		return $this->member_type;
	}
}

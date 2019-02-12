<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Contact Entity.
 *
 * @property int $id
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property string $user_id
 * @property \App\Model\Entity\User $user
 * @property int $member_id
 * @property \App\Model\Entity\Member $member
 * @property string $label
 * @property string $data
 */
class Contact extends Entity
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
	 * Return the contact data
	 * 
	 * @todo This could do formatting on the returned value. 
	 *		Or a task like that could be handled by Model validation tools. 
	 *		Or we can decide that user data should not be tampered with except 
	 *		in cases where it would effect system function/data integrity.
	 * 
	 * @return string
	 */
	public function getContact() {
		return $this->data;
	}
	
	/**
	 * Return the label for this contact
	 * 
	 * @return string (eg. 'phone', 'email', 'url', etc.)
	 */
	public function getLabel() {
		return $this->label;
	}
	
	
	public function asString($delimiter = ": ") {
		return $this->getLabel() . $delimiter . $this->getContact();
	}
	
	public function asArray() {
		return [$this->getLabel() => $this->getContact()];
	}
	
	public function isType($type) {
		return strtolower($type) === strtolower($this->getLabel());
	}
	
    /**
     * Is this marked as the primary contact
	 * 
	 * @todo There is no 'primary' column in the table yet
     * 
     * @return boolean
     */
    public function isPrimary() {
        return $this->primary > 0;
    }
}

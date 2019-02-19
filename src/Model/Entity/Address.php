<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Address Entity
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
    
    /**
     * Is this marked as the primary address
     * 
     * @return boolean
     */
    public function isPrimary() {
        return $this->primary_addr > 0;
    }
	
	/**
	 * Make a basic one-line address
	 * 
	 * Assemble "adress1, city, state zip" and 
	 * avoid uneeded puncutation and spaces. 
	 * Any of the 4 values may be missing. If none are 
	 * know, "Address unknown" is returned.
	 * 
	 * @return string
	 */
	public function asString() {
		$values = [$this->address1, $this->cityStateZip()];
		$address = implode(', ', $this->mergeStrings($values));
		
		return empty($address) ? 'Address unknown' : $address;
	}
	
	/**
	 * Make an array in standard address structure
	 * 
	 * A helper can walk through this and format it 
	 * as a label, or other typical multi-line format
	 * This carefully avoids blank lines and 
	 * unneccessary punctuations. 
	 * 
	 * There is no way to know how many elements there will be 
	 * and which data will be known because the process crushes out 
	 * any missing data and does some basic pre-assembly. 
	 * 
	 * The fullest possible return will be:
	 * `
	 * [
	 *		0 => 'address1 string',
	 *		1 => 'address2 string',
	 *		2 => 'address3 string',
	 *		3 => 'city string ' . 'state string ' . 'zip string'
	 * ]
	 * `
	 * Any of the 6 values may be missing and an empty array is possible.
	 * 
	 * @return array 
	 */
	public function asArray() {
		$props = ['address1', 'address2', 'address3'];
		$result = $this->mergeProps($props);
		
		$values = [$this->cityStateZip(), $this->country];
		return array_merge($result, $this->mergeStrings($values));
	}
	
	/**
	 * Merge a "city, state zip" line
	 * 
	 * Don't allow meaningless puctuation or spaces. 
	 * If no values are known, an empty string is returned. 
	 * 
	 * @return string
	 */
	public function cityStateZip() {
            $props = ['city', 'state', 'zip'];
            return implode(' ', $this->mergeProps($props));

            // version that does "city, state zip"
//            $props = ['city', 'state'];
//            $address = implode(', ', $this->mergeLines($props));
//
//            $values = [$sub, $this->zip];
//            return implode(' ', $this->mergeStrings($values));
	}
	
	/**
	 * Only add nodes if there is data to add
	 * 
	 * @param array $nodes
	 */
	private function mergeProps($props) {
            $result = [];
            foreach ($props as $prop) {
                if (!empty($this->{$prop})) {
                        $result[] = $this->$prop;
                }
            }
            return $result;
	}
	
	/**
	 * Only add nodes if there is data to add
	 * 
	 * @param array $nodes
	 */
	private function mergeStrings($strings) {
		$result = [];
		foreach ($strings as $string) {
			if (!empty($string)) {
				$result[] = $string;
			}
		}
		return $result;
	}
	
}

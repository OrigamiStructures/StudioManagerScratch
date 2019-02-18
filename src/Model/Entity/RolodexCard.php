<?php
namespace App\Model\Entity;

use App\Model\Entity\StackEntity;
use App\Lib\Layer;

define('ADDRESS', 'addresses');
define('CONTACT', 'contacts');
define('PRIMARY', 'primary');
define('BARE', 'bare');
define('WRAPPED', 'wrapped');

/**
 * Base class for detailed Person/Institution/Grouping objects
 * 
 * The system maintains a Member record that contains basic identifier data 
 * to describe who or what it represents and a few flag and indicator values 
 * to identify the nature of this individual units's role in the system is.
 * 
 * The Member record also serves as a gathering point for any number of 
 * Contact records (phone, url, email, whatever) and any number of Address 
 * records. 
 * 
 * One linked Contact and one linked Address can be designated as "primary" 
 * by setting the PRIMARY flag field in the Contact or Address.
 * 
 * Some Member records are attached to a Group record. The presence of this 
 * relationship indicates the Member acts as a category record that collects 
 * other Members into its group. 'The Drake Family', 'ABC Corp', or 
 * 'Woodworkers' are example groups.
 * 
 * When there is a Group record in the stack, there can also be content 
 * in the `has_members` layer. These will be basic data about the member 
 * records that are in the group.
 * 
 * And, of course some Member records will also be included as 
 * members of such a group. The `member_of` layer will hold basic data 
 * about the groups this unit is a member of.
 * 
 * The RolodexCard class works to gather all these associated elements together 
 * in a single manageable entity. It provides the basic structure and 
 * functionality for all of its concrete instantiations (PersonCard, 
 * InstitutionCard, GroupdCard, etc.)
 * 
 * <code>
 *	$Schema = [
 *		['name' => 'member', 'specs' => ['type' => 'layer']],
 *		['name' => 'contacts', 'specs' => ['type' => 'layer']],
 *		['name' => 'addresses', 'specs' => ['type' => 'layer']],
 *		['name' => 'member_of', 'specs' => ['type' => 'layer']],
 *		['name' => 'group', 'specs' => ['type' => 'layer']],
 *		['name' => 'has_members', 'specs' => ['type' => 'layer']],
 *	];
 * </code>
 *
 * @todo If a member has no contact or address, should it inherit from a 
 *		group? What if it is in more than one group? Would it need an 
 *		`inherit` flag? Would it need a `primary_group` pointer? Would 
 *		convenient upstream navigation satisfy the need for data? Would we 
 *		want a way to clone data down to the children?
 * 
 * @author dondrake
 */
class RolodexCard extends StackEntity {
		
    /**
     * Get the count of entities in a layer
     * 
     * @param string $layer
     * @return int
     */
    public function count($layer) {
		if (in_array($layer, ['primary_contact', 'primary_member', 'group']) && 
				!empty($this->$layer)){
			return 1;
		}
		return parent::count($layer);
	}
	
	/**
	 * Is this member a Group?
	 * 
	 * @return boolean
	 */
	public function isGroup() {
		if ($this->count('group')) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
    
	public function getName($format = FIRST_LAST) {
		return $this->member->element(0)->getName($format);
	}
	
	/**
	 * Get the id this member's group if there is one
	 * 
	 * @return string the group id or '' if this is not a group
	 */
	public function getGroupId() {
		if ($this->isGroup()) {
			return $this->group->id;
		}
		return '';
	}
	
// <editor-fold defaultstate="collapsed" desc="Accessing layers with PRIMARY">

	/**
	 * @see http://dev.os.com/article/stacks-layers-single-members-and-primary
	 */
	
	/**
	 * Is a record in the named layer flagged as PRIMARY?
	 * 
	 * @param string $type
	 * @return boolean
	 */
	public function hasPrimary($type) {
		$is_primary_arg = $this->$type->accessArgs()->property('primary');
		if ($this->_flagsPrimary($type) && !empty($this->$type->load('', 1, $is_primary_arg))) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get the entity flagged as PRIMARY if one exists
	 * 
	 * Return style allows the return to be wrapped in an array for 
	 * consistency (Helpers may expect arrays of entities) but also 
	 * allows a bare entity to return for convenience.
	 * 
	 * @param string $type Name of a layer that uses a PRIMARY flag column
	 * @param string $returnStyle WRAPPED or BARE
	 * @return mixed array, entity object, or null
	 */
	public function getPrimary($type, $returnStyle = WRAPPED) {
		if ($this->_flagsPrimary($type)) {
			$argObj = null;
			$result = $this->$type->load(PRIMARY, 1, $argObj);
		} else {
			$result = [];
		}
		if ($returnStyle === WRAPPED) {
			return $result;
		}
		return array_pop($result);
	}

	/**
	 * Get any entities not flagged as PRIMARY
	 * 
	 * @param string $type Name of a layer that uses a PRIMARY flag column
	 * @return array
	 */
	public function getSecondary($type) {
		if ($this->_flagsPrimary($type)) {
			$argObj = null;
			return $this->$type->load(PRIMARY, [null, 0], $argObj);
		}
		return [];
	}


	/**
	 * Does the named layer have a PRIMARY flag column
	 * 
	 * This method both defines the list of layers known to use a 
	 * PRIMARY flag, and confirst that the argument is one of those layers
	 * 
	 * @param string $type The name of a layer
	 * @return array
	 */
	protected function _flagsPrimary($type) {
		$primary = [ADDRESS, CONTACT];
		return in_array($type, $primary);
	}

// </editor-fold>
	
}

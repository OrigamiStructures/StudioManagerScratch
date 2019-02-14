<?php
namespace App\Model\Entity;

use App\Model\Entity\StackEntity;
use App\Lib\Layer;

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
 * Each Member can have one Contact and one Address designated as "primary".
 * 
 * Some Member records are attached to a Group record. The presence of this 
 * relationship indicates the Member acts as a category record that collects 
 * other Members into its group. ('The Drake Family', 'ABC Corp', or 
 * 'Woodworkers' are example Group Members).
 * 
 * This possibility means some Member records can also be included as 
 * members of such a group.
 * 
 * The RolodexCard class works to gather all these associated elements together 
 * in a single manageable entity. It provides the basic structure and 
 * functionality for all of its concrete instantiations
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
		if (in_array($layer, ['member', 'primary_contact', 'primary_member', 'group']) && 
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
		return $this->member->getName($format);
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
	
}

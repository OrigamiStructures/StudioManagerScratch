<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Lib\Traits\StackTrait;
use App\Lib\Layer;

/**
 * ArtStack Entity
 *
 * @property \App\Model\Artwork\Members $member
 * 
 */
class MemberStack extends StackEntity {
    	
	/**
	 * Name of the tip of this stack
	 *
	 * @var string
	 */
	protected $_primary = 'member';

	/**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
    ];
	
}

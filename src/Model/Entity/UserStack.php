<?php
namespace App\Model\Entity;

use App\Lib\Layer;
use App\Model\Entity\StackEntity;

/**
 * ArtStack Entity
 *
 * @property \App\Model\Artwork\User $user
 * 
 */
class UserStack extends StackEntity {
    	
	/**
	 * Name of the tip of this stack
	 *
	 * @var string
	 */
	protected $_primary = 'user';

	/**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
    ];
	
}

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Lib\Traits\StackTrait;
use App\Lib\Layer;

/**
 * ArtStack Entity
 *
 * @property \App\Model\Artwork\??? $???
 * 
 */
class ???Stack extends StackEntity {
    	
	/**
	 * Name of the tip of this stack
	 *
	 * @var string
	 */
	protected $_primary = ???;

	/**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
    ];
	
}

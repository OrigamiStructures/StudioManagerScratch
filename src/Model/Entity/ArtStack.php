<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Lib\Traits\StackTrait;
use App\Lib\Layer;

/**
 * ArtStack Entity
 *
 * @property \App\Model\Artwork\Artwork $artwork
 * @property \App\Model\Entity\Edition[] $editions
 * @property \App\Model\Entity\Format[] $formats
 * @property \App\Model\Entity\Piece[] $pieces
 */
class ArtStack extends Entity {
    
    use StackTrait;
	
	protected $_primary = 'artwork';

	/**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
    ];
    
}

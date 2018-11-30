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
 * @property \CakeORM\Entity[] $dispositionsPieces
 * 
 */
class ArtStack extends StackEntity {
    	
	/**
	 * Name of the tip of this stack
	 *
	 * @var string
	 */
	protected $_primary = 'artwork';

	/**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
    ];
	
	public function emitEditionStack($id, $fromLayer = 'edition') {
		if (!in_array($fromLayer, ['edition', 'format', 'piece'])) {
			return [];
		}
		
	}
	
	public function emitFormatStack($id, $fromLayer = 'format') {
		 
	}
	
	public function emitPieceStack($id) {
		
	}
	
}

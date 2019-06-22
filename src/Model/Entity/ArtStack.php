<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Lib\Layer;

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
	 * @todo Let StackTable::marshalStack() set this
	 * {@inheritdoc}
	 */
	protected $rootName = 'artwork';
	
	/**
	 * @todo Let StackTable::marshalStack() set this
	 * {@inheritdoc}
	 */
	public $rootDisplaySource = 'title';

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
	
	public function title() {
		return $this->rootDisplayValue();
	}
	
	public function description() {
		return $this->rootElement()->description;
	}
	
	public function isFlat() {
		return $this->editions->count() === 1 && $this->pieces->count() === 1;
	}
}

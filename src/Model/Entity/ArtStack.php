<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\Lib\Layer;

/**
 * ArtStack Entity
 *
 * @property Layer $artwork
 * @property Layer $editions
 * @property Layer $formats
 * @property Layer $pieces
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
    //</editor-fold>

	public function title() {
		return $this->rootDisplayValue();
	}

	public function description() {
		return $this->rootElement()->description;
	}

	public function isFlat() {
		return $this->editions->count() === 1
				&& $this->editions
					->element(0, LAYERACC_INDEX)
					->type === EDITION_UNIQUE;
	}
}

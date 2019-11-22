<?php
namespace App\Model\Entity;

use App\Model\Lib\Providers;
use Cake\ORM\Entity;
use App\Model\Lib\Layer;
use Cake\Utility\Text;
use mysql_xdevapi\Exception;

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
        '*' => true
        /* broke test StackEntityTest::testSet() line 442  */
//        'artwork' => true,
//        'editions' => true,
//        'formats' => true,
//        'pieces' => true,
//        'series' => true
    ];

    //<editor-fold desc="ID getters">
    public function artworkId()
    {
        return $this->rootID();
    }
    /**
     * @return array
     */
    public function editionIDs()
    {
        return $this->IDs('editions');
    }

    /**
     * @return array
     */
    public function formatIDs()
    {
        return $this->IDs('formats');
    }

    /**
     * @return array
     */
    public function pieceIDs()
    {
        return $this->IDs('pieces');
    }

    /**
     * @return array
     */
    public function seriesIDs()
    {
        return $this->IDs('series');
    }
    //</editor-fold>

    //<editor-fold desc="Object Emitters">

    public function oldEditionStack($editionId)
    {
        if(!in_array($editionId, $this->editionIDs())) {
            $idList = Text::toList($this->editionIDs());
            $msg = "Invalid edition id ($editionId). {$this->title()} had edition IDs $idList" ;
            throw new \Exception($msg);
        }
        $artwork = $this->artwork->shift();
        $edition = $this->editions->element($editionId, LAYERACC_ID);
        $formats = $this->formats->linkedTo('edition', $editionId)->toArray();
        $pieces = $this->pieces->linkedTo('edition', $editionId)->toArray();
        $providers = ['edition' => $edition] + $formats;
        $stack = [
            'providers' => new Providers($providers),
            'pieces' => ($pieces),
            'artwork' => $artwork,
        ];
        return $stack;
    }
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

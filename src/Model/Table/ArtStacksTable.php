<?php
namespace App\Model\Table;

use App\Model\Table\StacksTable;
use App\Model\Lib\Layer;
use Cake\Cache\Cache;
use App\Model\Lib\StackSet;
use App\Model\Entity\ArtStack;
use App\Cache\ArtStackCacheTools as cacheTools;
use App\SiteMetrics\CollectTimerMetrics;
use Cake\ORM\TableRegistry;

/**
 * ArtStacks Model
 *
 * @property \App\Model\Table\ArtworkTable $Artworks
 * @property \App\Model\Table\EditionsTable $Editions
 * @property \App\Model\Table\FormatsTable $Formats
 * @property \App\Model\Table\PiecesTable $Pieces
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\Core\ConventionsTrait
 */
class ArtStacksTable extends StacksTable
{

	/**
	 * {@inheritdoc}
	 */
	protected $rootName = 'artwork';

	protected $rootTable = 'Artworks';

	/**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
		$this->setTable('artworks');
		$this->addStackSchema(['artwork', 'editions', 'series', 'formats', 'pieces', 'dispositions_pieces']);
		$this->addLayerTable(['Artworks', 'Editions', 'Series', 'Formats', 'EditionsFormats', 'Pieces']);
		$this->addSeedPoint([
            'disposition', 'dispositions',
            'piece', 'pieces',
            'format', 'formats',
            'edition', 'editions',
            'artwork', 'artworks',
            'series',
        ]);
        parent::initialize($config);
    }

// <editor-fold defaultstate="collapsed" desc="Concrete Start-from implementations">

	/**
	 * Load the artwork stacks to support these artworks
	 *
	 * @param array $ids Artwork ids
	 * @return StackSet
	     */
	protected function distillFromArtwork($ids) {
		return $this->Artworks
				->find('all')
				->where(['id IN' => $ids])
			;
	}

	/**
	 * Load the artwork stacks to support these editions
	 *
	 * @param array $ids Edition ids
	 * @return StackSet
	 */
	protected function distillFromEdition($ids) {
		$query = $this->Editions->find('list', ['valueField' => 'artwork_id'])
				->where(['id IN' => $ids])
				;
		if ($query->count() !== 0) {
			return $this->distillFromArtwork($query->toArray());
		} else {
			return $query;
		}
	}

	/**
	 * Load the artwork stacks to support these formats
	 *
	 * @param array $ids Format ids
	 * @return StackSet
	 */
	protected function distillFromFormat($ids) {
		$query = $this->EditionsFormats->find('list', ['valueField' => 'edition_id'])
				->where(['format_id IN' => $ids]);
		if ($query->count() !== 0) {
			return $this->distillFromEdition($query->toArray());
		} else {
			return $query;
		}
	}

	/**
	 * Load the artwork stacks to support these pieces
	 *
	 * @param array $ids Piece ids
	 * @return StackSet
	 */

	protected function distillFromPiece($ids) {
		$query = $this->Pieces->find('list', ['valueField' => 'edition_id'])
				->where(['id IN' => $ids]);
		if ($query->count() !== 0) {
			return $this->distillFromEdition($query->toArray());
		} else {
			return $query;
		}
	}

	/**
	 * Load the artwork stacks to support these dispositions
	 *
	 * @param array $ids Disposition ids
	 * @return StackSet
	 */

	protected function distillFromDisposition($ids) {
		$DispositionsPieces = TableRegistry::getTableLocator()->get('DispositionsPieces');
		$query = $DispositionsPieces->find('list', ['valueField' => 'piece_id'])
				->where(['disposition_id IN' => $ids]);
		if ($query->count() !== 0) {
			return $this->distillFromPiece($query->toArray());
		} else {
			return $query;
		}
	}

	/**
	 * Load the artwork stacks to support these series
	 *
	 * @param array $ids Series ids
	 * @return StackSet
	 */

	protected function distillFromSeries($ids) {
		return $this->Editions->find('inSeries', $ids)
            ->select(['id', 'artwork_id', 'series_id'])
        ;
//		return $editions->distinct('artwork_id');
	}

// </editor-fold>

	/**
	 * Read the stack from cache or assemble it and cache it
	 *
	 * This is an alternate finder for cases where you have a set
	 * of Artworks id.
	 *
	 * @param array $ids Artwork ids
	 * @return StackSet
	 */
    public function stacksFromArtworks($ids) {
		return $this->stacksFromRoot($ids);

//        if (!is_array($ids)) {
//            $msg = "The ids must be provided as an array.";
//            throw new \BadMethodCallException($msg);
//        }
//
//		$t = CollectTimerMetrics::instance();
//
//        $this->stacks = new StackSet();
//
//        foreach ($ids as $id) {
//            $le = $t->startLogEntry("ArtStack.$id");
//            $stack = FALSE;
//            $t->start("read", $le);
//            $stack = Cache::read(cacheTools::key($id), cacheTools::config());
//            $t->end('read', $le);
//
//            if (!$stack && !$this->stacks->isMember($id)) {
//                $t->start("build", $le);
//                $stack = $this->newEntity([]);
//
//                $artwork = $this->Artworks->find('artworks', ['values' => [$id]]);
//                    $stack->set('artwork', $artwork->toArray());
//
//                if ($stack->count('artwork')) {
//                    $editions = $this->Editions->find('inArtworks', ['values' => [$id]]);
//                    $stack->set('editions', $editions->toArray());
//                    $editionIds = $stack->editions->IDs();
//                }
//
//                if ($stack->count('editions')) {
//                    $formats = $this->Formats->find('inEditions', ['values' => $editionIds]);
//                    $pieces = $this->Pieces->find('inEditions', ['values' => $editionIds]);
//                    $stack->set([
//                        'formats' => $formats->toArray(),
//                        'pieces' => $pieces->toArray(),
//                        ]);
//                    $pieceIds = $stack->pieces->IDs();
//                }
//
//                if ($stack->count('pieces')) {
//                    $dispositions_pieces = $this->
//                        _distillFromJoinTable('DispositionsPieces', 'piece_id', $pieceIds);
//                    $stack->set('dispositions_pieces', $dispositions_pieces->toArray());
//                }
//
//                $t->end('build', $le);
//                $t->start("write", $le);
////                Cache::write(cacheTools::key($id), $stack, cacheTools::config());
//                $t->end('write', $le);
//            }
//
//            $t->logTimers($le);
//
//            if ($stack->count('artwork')) {
//                $stack->clean();
//                $this->stacks->insert($id, $stack);
//            }
//        }
//
//        return $this->stacks;
    }

	/**
	 * 'artwork',
	 * 'editions',
	 * 'formats',
	 * 'pieces',
	 * 'dispositions_pieces'
	 */

	public function marshalArtwork($id, $stack) {
		$artwork = $this->Artworks->find('artworks', ['values' => [$id]]);
		$stack->set('artwork', $artwork->toArray());
		return $stack;
	}

	public function marshalEditions($id, $stack) {
		if ($stack->count('artwork')) {
			$editions = $this->Editions->find('inArtworks', ['values' => [$id]]);
			$stack->set('editions', $editions->toArray());
		}
		return $stack;
	}

	public function marshalSeries($id, $stack) {
		if ($stack->count('editions')) {
			$series_ids = $stack->editions->toDistinctList('series_id');
			$series_ids = empty($series_ids) ? [''] : $series_ids;
			$series = $this->Series->find('all')
					->where(['id IN' => $series_ids]);
			$stack->set('series', $series->toArray());
		}
		return $stack;
	}

	public function marshalFormats($id, $stack) {
		if ($stack->count('editions')) {
			$editionIds = $stack->editions->IDs();
			$formats = $this->EditionsFormats->find('inEditions', ['values' => $editionIds]);
			$stack->set(['formats' => $formats->toArray()]);
		}
		return $stack;
	}

	public function marshalPieces($id, $stack) {
		if ($stack->count('editions')) {
			$editionIds = $stack->editions->IDs();
			$pieces = $this->Pieces->find('inEditions', ['values' => $editionIds]);
			$stack->set(['pieces' => $pieces->toArray()]);
		}
		return $stack;
	}

	public function marshalDispositionsPieces($id, $stack) {
		if ($stack->count('pieces')) {
			$pieceIds = $stack->pieces->IDs();
			$dispositions_pieces = $this->
				_distillFromJoinTable('DispositionsPieces', 'piece_id', $pieceIds);
			$stack->set('dispositions_pieces', $dispositions_pieces->toArray());
		}
		return $stack;
	}

}

<?php
namespace App\Model\Table;

use App\Model\Table\StacksTable;
use App\Model\Lib\Layer;
use Cake\Cache\Cache;
use App\Model\Lib\StackSet;
use App\Model\Entity\ArtStack;
use App\Cache\ArtStackCacheTools as cacheTools;
use App\SiteMetrics\CollectTimerMetrics;

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

	/**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
		$this->setTable('artworks');
		$this->addStackSchema(['artwork', 'editions', 'series', 'formats', 'pieces', 'dispositions_pieces']);
		$this->addLayerTable(['Artworks', 'Editions', 'Series', 'Formats', 'Pieces']);
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
		return $ids;
	}

	/**
	 * Load the artwork stacks to support these editions
	 * 
	 * @param array $ids Edition ids
	 * @return StackSet
	 */
	protected function distillFromEdition($ids) {
		$editions = new Layer($this
            ->_loadLayer('edition', $ids)
            ->select(['id', 'artwork_id'])
            ->toArray(), 
            'editions' // this is the second Layer arg
		);
        if ($editions->count()) {
            $result = $editions->distinct('artwork_id');
        } else {
            $result = [];
        }
		return $result;
	}

	/**
	 * Load the artwork stacks to support these formats
	 * 
	 * @param array $ids Format ids
	 * @return StackSet
	 */
	protected function distillFromFormat($ids) {
		$formats = new Layer($this
            ->_loadLayer('formats', $ids)
            ->select(['id', 'edition_id'])
            ->toArray(), 
            'formats' // this is the second Layer arg
		);
        if ($formats->count()) {
            $editions = new Layer($this
                    ->_loadLayer('edition', $formats->distinct('edition_id'))
                    ->select(['id', 'artwork_id'])
                    ->toArray()
            );
        }
        if (isset($editions) && $editions->count()) {
            $result = $editions->distinct('artwork_id');
        } else {
            $result = [];
        }
		return $result;
	}

	/**
	 * Load the artwork stacks to support these pieces
	 * 
	 * @param array $ids Piece ids
	 * @return StackSet
	 */

	protected function distillFromPiece($ids) {
		$pieces = new Layer($this
            ->_loadLayer('pieces', $ids)
            ->select(['id', 'edition_id'])
            ->toArray(), 
            'pieces' // this is the second Layer arg
		);
        if ($pieces->count()) {
            $editions = new Layer($this
                ->_loadLayer('edition', $pieces->distinct('edition_id'))
                ->select(['id', 'artwork_id'])
                ->toArray()
            );
        }
        if (isset($editions) && $editions->count()) {
            $result = $editions->distinct('artwork_id');
        } else {
            $result = [];
        }
		return $result;
	}

	/**
	 * Load the artwork stacks to support these dispositions
	 * 
	 * @param array $ids Disposition ids
	 * @return StackSet
	 */

	protected function distillFromDisposition($ids) {
		$joins = $this->
				_distillFromJoinTable('DispositionsPieces', 'disposition_id', $ids);
        if ($joins->count()) {
            $dispositionPieces = $this->
                dispositions_pieces = new Layer($joins->toArray());
        }
        if (isset($dispositionPieces) && $dispositionPieces->count()) {
            $pieces = new Layer($this
                ->_loadLayer('pieces', $dispositionPieces->distinct('piece_id'))
                ->select(['id', 'edition_id'])
                ->toArray()
            );
        }  
        if (isset($pieces) && $pieces->count()) {
            $editions = new Layer($this
                ->_loadLayer('edition', $pieces->distinct('edition_id'))
                ->select(['id', 'artwork_id'])
                ->toArray()
            );
        }     
        if (isset($editions) && $editions->count()) {
            $result = $editions->distinct('artwork_id');
        } else {
            $result = [];
        }
		return $result;
	}

	/**
	 * Load the artwork stacks to support these series
	 * 
	 * @param array $ids Series ids
	 * @return StackSet
	 */

	protected function distillFromSeries($ids) {
		$editions = new Layer($this
            ->Editions->find('inSeries', $ids)
            ->select(['id', 'artwork_id', 'series_id'])
            ->toArray(), 'series'
		);
		return $editions->distinct('artwork_id');
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
			$series_ids = $stack->editions->distinct('series_id');
			$series_ids = empty($series_ids) ? [''] : $series_ids;
			$series = $this->Series->find('all')
					->where(['id IN' => $series_ids]);
			$stack->set(['series' => $series->toArray()]);
		} 
		return $stack;
	}
	
	public function marshalFormats($id, $stack) {
		if ($stack->count('editions')) {
			$editionIds = $stack->editions->IDs();
			$formats = $this->Formats->find('inEditions', ['values' => $editionIds]);
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

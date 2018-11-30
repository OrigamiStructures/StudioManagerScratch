<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use App\Lib\Stacks;
use App\Lib\Layer;
use Cake\Core\ConventionsTrait;
use Cake\Cache\Cache;
use App\Model\Entity\ArtStack;
use App\Model\Lib\StackSet;
use Cake\Database\Schema\TableSchema;
use Cake\Core\Configure;
use App\SiteMetrics\CollectTimerMetrics;
use App\Cache\ArtStackCacheTools as cacheTools;

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
class ArtStacksTable extends Table
{
    
    use ConventionsTrait;
	
	/**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);
    }
    
	/**
	 * Lazy load the required tables
	 * 
	 * I couldn't get Associations to work in cooperation with the schema 
	 * initialization that sets the custom 'layer' type properties. This is 
	 * my solution to making the Tables available 
	 * 
	 * @param string $property
	 * @return Table|mixed
	 */
    public function __get($property) {
        if (in_array($property, ['Artworks', 'Editions', 'Formats', 'Pieces'])) {
            return TableRegistry::getTableLocator()->get($property);
		}
        return parent::__get($property);
    }
    
	/**
	 * Add the columns to hold the different layers and set their data type
	 * 
	 * This will make the entity properties automatically 
	 * contain Layer objects. 
	 * 
	 * @param TableSchema $schema
	 * @return TableSchema
	 */
	protected function _initializeSchema(TableSchema $schema) {
		$schema->addColumn('artwork', ['type' => 'layer']);
		$schema->addColumn('editions', ['type' => 'layer']);
		$schema->addColumn('formats', ['type' => 'layer']);
		$schema->addColumn('pieces', ['type' => 'layer']);
		$schema->addColumn('dispositionsPieces', ['type' => 'layer']);
        return $schema;
    }
	
	/**
	 * The primary access point to get ArtStacks
	 * 
	 * The stacks are meant to provide full context for other detail 
	 * data sets that have been retirieved for some process. This allows 
	 * working data queries to be small and focused. Once completed, the 
	 * Stack tables back-fill the context.
	 * 
	 * $options requires two indexes, 
	 *		'layer' with a value matching any allowed starting point 
	 *		'ids' containing an array of ids for the named layer
	 * 
	 * <code>
	 * $ArtStacks->find('stackFrom',  ['layer' => 'disposition', 'ids' => $ids]);
	 * $ArtStacks->find('stackFrom',  ['layer' => 'artworks', 'ids' => $ids]);
	 * $ArtStacks->find('stackFrom',  ['layer' => 'format', 'ids' => $ids]);
	 * </code>
	 * 
	 * @param Query $query
	 * @param array $options
	 * @return StackSet
	 * @throws \BadMethodCallException
	 */
	public function findStackFrom($query, $options) {
		$allowedStartPoints = [
			'disposition', 'dispositions',
			'piece', 'pieces', 'format', 'formats',
			'edition', 'editions', 'artwork', 'artworks',
			'series',
		];
        extract($options); //expects $layer string and $ids array
		
		if (in_array($layer, $allowedStartPoints)) {
			$method = 'loadFrom' . $this->_entityName($layer);
			return $this->$method($ids);
		}
		$msg = "ArtStacks can't do lookups starting from $layer";
		throw new \BadMethodCallException($msg);
        
    }
    
// <editor-fold defaultstate="collapsed" desc="Concrete Start-from implementations">
	
	/**
	 * Load the artwork stacks to support these artworks
	 * 
	 * @param array $ids Artwork ids
	 * @return StackSet
	     */
	protected function loadFromArtwork($ids) {
		return $this->stacksFromAtworks($ids);
	}

	/**
	 * Load the artwork stacks to support these editions
	 * 
	 * @param array $ids Edition ids
	 * @return StackSet
	 */
	protected function loadFromEdition($ids) {
		$editions = new Layer($this
						->_loadLayer('edition', $ids)
						->select(['id', 'artwork_id'])
						->toArray()
		);
		return $this->stacksFromAtworks($editions->distinct('artwork_id'));
	}

	/**
	 * Load the artwork stacks to support these formats
	 * 
	 * @param array $ids Format ids
	 * @return StackSet
	 */
	protected function loadFromFormat($ids) {
		$formats = new Layer($this
						->_loadLayer('formats', $ids)
						->select(['id', 'edition_id'])
						->toArray()
		);
		$editions = new Layer($this
						->_loadLayer('edition', $formats->distinct('edition_id'))
						->select(['id', 'artwork_id'])
						->toArray()
		);
		return $this->stacksFromAtworks($editions->distinct('artwork_id'));
	}

	/**
	 * Load the artwork stacks to support these pieces
	 * 
	 * @param array $ids Piece ids
	 * @return StackSet
	 */

	protected function loadFromPiece($ids) {
		$pieces = new Layer($this
						->_loadLayer('pieces', $ids)
						->select(['id', 'edition_id'])
						->toArray()
		);
		$editions = new Layer($this
						->_loadLayer('edition', $pieces->distinct('edition_id'))
						->select(['id', 'artwork_id'])
						->toArray()
		);
		return $this->stacksFromAtworks($editions->distinct('artwork_id'));
	}

	/**
	 * Load the artwork stacks to support these dispositions
	 * 
	 * @param array $ids Disposition ids
	 * @return StackSet
	 */

	protected function loadFromDisposition($ids) {
		$joins = $this->
				_loadFromJoinTable('DispositionsPieces', 'disposition_id', $ids);
		$dispositionPieces = $this->
				dispositionsPieces = new Layer($joins->toArray());
		$pieces = new Layer($this
						->_loadLayer('pieces', $dispositionPieces->distinct('piece_id'))
						->select(['id', 'edition_id'])
						->toArray()
		);
		$editions = new Layer($this
						->_loadLayer('edition', $pieces->distinct('edition_id'))
						->select(['id', 'artwork_id'])
						->toArray()
		);
		return $this->stacksFromAtworks($editions->distinct('artwork_id'));
	}

	/**
	 * Load the artwork stacks to support these series
	 * 
	 * @param array $ids Series ids
	 * @return StackSet
	 */

	protected function loadFromSeries($ids) {
		$editions = new Layer($this
						->Editions->find('inSeries', $ids)
						->select(['id', 'artwork_id', 'series_id'])
						->toArray()
		);
		return $this->stacksFromAtworks($editions->distinct('artwork_id'));
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
    public function stacksFromAtworks($ids) {
		$t = CollectTimerMetrics::instance();
		
        $this->stacks = new StackSet();
		
        foreach ($ids as $id) {
			$le = $t->startLogEntry("ArtStack.$id");
            $stack = FALSE;
			$t->start("read", $le);
            $stack = Cache::read(
                cacheTools::key($id), 
                cacheTools::config()
                );
			$t->end('read', $le);
            
            if (!$stack && !$this->stacks->isMember($id)) {
				$t->start("build", $le);
                $stack = new ArtStack();
                
                $artwork = $this->Artworks->find('artworks', ['values' => [$id]]);
                $stack = $this->_marshall($stack, 'artwork', $artwork->toArray());
                
                $editions = $this->Editions->find('inArtworks', ['values' => [$id]]);
                $stack = $this->_marshall($stack, 'editions', $editions->toArray());

				$editionIds = $stack->editions->IDs();
                
                $formats = $this->Formats->find('inEditions', ['values' => $editionIds]);
                $stack = $this->_marshall($stack, 'formats', $formats->toArray());
                
                $pieces = $this->Pieces->find('inEditions', ['values' => $editionIds]);
                $stack = $this->_marshall($stack, 'pieces', $pieces->toArray());
                
                $pieceIds = $stack->pieces->IDs();
                
                $dispositionsPieces = $this->
                    _loadFromJoinTable('DispositionsPieces', 'piece_id', $pieceIds);
                $stack = $this->_marshall(
						$stack, 
						'dispositionsPieces', 
						$dispositionsPieces->toArray());
				$t->end('build', $le);

				$t->start("write", $le);
                Cache::write(
						cacheTools::key($id), 
						$stack, 
						cacheTools::config()
					);
				$t->end('write', $le);
            }
        
            $t->logTimers($le);
            $this->stacks->insert($id, $stack);
        }
			
        return $this->stacks;
    }
	    
// <editor-fold defaultstate="collapsed" desc="Probably goes in a Stack parent class">
	
	/**
	 * Load members of a table by id
	 * 
	 * The table name will be deduced from the $layer. Also, there is the 
	 * assumption that a custom finder exists in that Table which is in the form 
	 * Table::findTable() which can do an single or array id search.
	 * Custom finders based on IntegerQueryBehavior do the job in this system.
	 * 
	 * <code>
	 * $this-_loadLayer('member', $ids);
	 * 
	 * //will evaluate to
	 * $this->Members->find('members', ['values' => $ids]);
	 * 
	 * //and will expect, in the Members Table the custom finder:
	 * public function findMembers($query, $options) {
	 *      //must properly handle an array of id values
	 *      //finders us
	 * }
	 * </code>
	 * 
	 * @param name $layer The  
	 * @param array $ids
	 * @return Query A new query on some table
	     */
	private function _loadLayer($layer, $ids) {
		$tableName = $this->_modelNameFromKey($layer);
		$finderName = lcfirst($tableName);
        
		return $this->$tableName
						->find($finderName, ['values' => $ids]);
	}

	/**
	 * Set one of the layer properties for the Stack type entity
	 * 
	 * The value must be a homogenous array of entities
	 * 
	 * @param Entity $entity
	 * @param string $property The property to set
	 * @param array $value An array of Entities
	     */
	public function _marshall($entity, $property, $value) {
		$this->patchEntity($entity, [$property => $value]);
		$entity->setDirty($property, FALSE);
		return $entity;
	}

	/**
	 * Throw together a temporary Join Table class and search it
	 * 
	 * This will actually work for any table, but habtm tables typically 
	 * don't have a named class written for them.
	 * 
	 * 
	 * @param string $table The name of the table class by convention
	 * @param string $column Name of the integer column to search
	 * @param array $ids
	     */
	protected function _loadFromJoinTable($table, $column, $ids) {
		$joinTable = TableRegistry::getTableLocator()
				->get($table)
				->addBehavior('IntegerQuery');

		$q = $joinTable->find('all');
		$q = $joinTable->integer($q, $column, $ids);
		return $q;
	}
// </editor-fold>

}

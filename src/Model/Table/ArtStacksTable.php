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

/**
 * ArtStacks Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\ImagesTable|\Cake\ORM\Association\BelongsTo $Images
 * @property \App\Model\Table\EditionsTable|\Cake\ORM\Association\HasMany $Editions
 *
 * @method \App\Model\Entity\ArtStack get($primaryKey, $options = [])
 * @method \App\Model\Entity\ArtStack newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ArtStack[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ArtStack|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ArtStack|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ArtStack patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ArtStack[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ArtStack findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
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
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->_initializeAssociations();
    }
    
//    protected function setSchema($schema) {
//        
//    }
    
    protected function _initializeAssociations(){
		$this->hasMany('artworks');
		$this->hasMany('editions');
		$this->hasMany('formats');
		$this->hasMany('pieces');
		$this->hasMany('dispositions_pieces');
    }
    
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
	protected function _loadFromArtwork($ids) {
		return $this->stacksFromAtworks($ids);
	}

	/**
	 * Load the artwork stacks to support these editions
	 * 
	 * @param array $ids Edition ids
	 * @return StackSet
	 */
	protected function _loadFromEdition($ids) {
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
	 * @param array $ids Artwork ids
	 * @return StackSet
	 */
    public function stacksFromAtworks($ids) {
		
        $this->stacks = new StackSet(); //temporary solution, should be an object?
		
        foreach ($ids as $id) {
            $stack = FALSE;
//            $stack = Cache::read(
//                $StackCache->key('art', $id), 
//                $StackCache->config('art'));
            
            if (!$stack && !key_exists($id, $this->stacks)) {
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

//                Cache::write(
//                    $StackCache->key('art', $id), $stack, 
//                    $StackCache->config('art'));
            }
            
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
//		$this->patchEntity($entity, $value);
		$entity->set($property, new Layer($value));
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

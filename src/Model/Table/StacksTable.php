<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Core\ConventionsTrait;
use App\Model\Lib\StackSet;
use Cake\Database\Schema\TableSchema;
use App\Exception\UnknownTableException;

/**
 * StacksTable Model
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\Core\ConventionsTrait
 */
class StacksTable extends Table
{
    
    use ConventionsTrait;
    
    /**
     *
     * @var array
     */
    protected $layerTables = [];
    
    /**
     *
     * @var array
     */
    protected $stackSchema = [];
    
    /**
     *
     * @var array
     */
    protected $seedPoints = [];


    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        //Check if proper table is created
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
		
        if (in_array($property, $this->layerTables)) {
            $this->$property = TableRegistry::getTableLocator()->get($property);
			return $this->$property;
		}
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
        foreach ($this->stackSchema as $column) {
				$schema->addColumn($column['name'], $column['specs']);
        }
        return $schema;
    }
	
	/**
	 * The primary access point to get a concrete stack
	 * 
	 * Stacks are meant to provide full context for other detail 
	 * data sets that have been retrieved for some process. This allows 
	 * working data queries to be small and focused. Once completed, the 
	 * Stack tables back-fill the context.
	 * 
	 * $options requires two indexes, 
	 *		'seed' with a value matching any allowed starting point 
	 *		'ids' containing an array of ids for the named seed
	 * 
	 * <code>
	 * $ArtStacks->find('stacksFor',  ['seed' => 'disposition', 'ids' => $ids]);
	 * $ArtStacks->find('stacksFor',  ['seed' => 'artworks', 'ids' => $ids]);
	 * $ArtStacks->find('stacksFor',  ['seed' => 'format', 'ids' => $ids]);
	 * </code>
	 * 
	 * @param Query $query
	 * @param array $options
	 * @return StackSet
	 * @throws \BadMethodCallException
	 */
	public function findStacksFor($query, $options) {
        
        $this->validateArguments($options);
        extract($options); //$seed, $ids
        if (empty($ids)) {
            return new StackSet();
        }
        $method = 'distillFrom' . $this->_entityName($seed);
        return $this->$method($ids);
    }
    
// <editor-fold defaultstate="collapsed" desc="finder args validation">

    /**
     * Insure the findStack arguments were correct
     * 
     * @return void
     * @throws \BadMethodCallException
     */
    protected function validateArguments($options) {
        $msg = FALSE;
        if (!array_key_exists('seed', $options) || !array_key_exists('ids', $options)) {
            $msg = "Options array argument must include both 'seed' and 'ids' keys.";
            throw new \BadMethodCallException($msg);
        }

        if (!is_array($options['ids'])) {
            $msg = "The ids must be provided as an array.";
        } elseif (!in_array($options['seed'], $this->seedPoints)) {
            $msg = "{$this->getRegistryAlias()} can't do lookups starting from {$options['seed']}";
        }
        if ($msg) {
            throw new \BadMethodCallException($msg);
        }
        return;
    }

// </editor-fold>
	
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
    protected function _loadLayer($layer, $ids) {
		$tableName = $this->_modelNameFromKey($layer);
		$finderName = lcfirst($tableName);
        
		return $this->$tableName
						->find($finderName, ['values' => $ids]);
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
	protected function _distillFromJoinTable($table, $column, $ids) {
		$joinTable = TableRegistry::getTableLocator()
				->get($table)
				->addBehavior('IntegerQuery');

		$q = $joinTable->find('all');
		$q = $joinTable->integer($q, $column, $ids);
		return $q;
	}

	public function hasSeed($name) {
		return in_array($name, $this->seedPoints);
	}

    /**
     * Add layer tables
     *
     * Check to be sure that the added tables are all valid tables
     *
     * @throws UnknownTableException
     * @param array $addedTables
     */
	public function addLayerTable(array $addedTables)
    {
        foreach ($addedTables as $index => $addedTable) {
            if(is_a(TableRegistry::getTableLocator()->get($addedTable), 'Cake\ORM\Table')){
                $this->layerTables[] = $addedTable;
            } else {
                throw new UnknownTableException("StacksTable initialization discovered
                $addedTable is not a valid table name");
            }
        }
        $this->layerTables = array_unique($this->layerTables);
	}
}

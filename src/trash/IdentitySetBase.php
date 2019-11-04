<?php
namespace App\Model\Lib;

use Cake\ORM\TableRegistry;
use Cake\Core\ConventionsTrait;
use App\Model\Entity\Traits\MapReduceIndexerTrait;

/**
 * IdentitySetBase
 *
 * Provide common methods and properties for individual and groups of
 * IdentitySet objects
 *
 * @author dondrake
 */
abstract class IdentitySetBase {

	use ConventionsTrait;
	use MapReduceIndexerTrait;

	/**
	 * Type of originating entity
	 *
	 * @var string
	 */
	protected $source_entity_name;

	/**
	 * Name of the entities referenced by IDs in the list
	 *
	 * @var string
	 */
	protected $_table_name;

	/**
	 * The set of entites tracked in the set(s)
	 *
	 * @var array
	 */
	protected $_entities;

	abstract public function count();

	abstract public function has($id);

	abstract public function sourceFor($id);

	abstract public function idList();


	/**
	 * Start and return a new query for them members in these sets
	 *
	 * @return Query
	 */
	public function query() {
		$table = TableRegistry::getTableLocator()->get($this->table());
		return $table->find('all', ['conditions' => [
				'id IN ' => $this->idList(),
			]])->mapReduce([$this, 'indexer'], [$this, 'passThrough']);
	}

	/**
	 * Return an array of entities for the members of this single set
	 *
	 * @return array
	 */
	public function arrayResult() {
		$this->_entities = $this->query()->toArray();
		return $this->_entities;
	}

	/**
	 *
	 * @param type $id
	 * @return type
	 */
	public function entity($id = NULL) {
		if (!isset($this->_entities)) {
			$this->arrayResult();
		}
		if (is_null($id)) {
			return $this->_entities;
		} else {
			return $this->_entities[$id];
		}
	}

	/**
	 * Get the table name for the members in the sets
	 *
	 * @return string
	 */
	public function table() {
		return $this->_table_name;
	}

	/**
	 * Get the entity name for the source entities
	 *
	 * @return string
	 */
	public function sourceName() {
		return $this->source_entity_name;
	}

}

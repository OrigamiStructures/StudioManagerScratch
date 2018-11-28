<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use App\Lib\Stacks;
use App\Lib\Layer;
use Cake\Core\ConventionsTrait;
use Cake\Cache\Cache;
use App\Model\Lib\StackSet;
use Cake\Database\Schema\TableSchema;
use Cake\Core\Configure;
use App\SiteMetrics\CollectTimerMetrics;
use App\Cache\UserStackCacheTools as cacheTools;
use App\Lib\SystemState;
use App\Model\Entity\UserStack;

/**
 * UserStacks Model
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\MembersTable $Members
 * @property \App\Model\Table\ContactsTable $Contacts
 * @property \App\Model\Table\AddressesTable $Addresses
 * @property \App\Model\Table\AritstsTable $Artists
 * @property \App\Model\Table\GroupMembersTable $GroupMembers
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\Core\ConventionsTrait
 */
class UserStacksTable extends Table
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
	 * intialization that sets the custom 'layer' type properties. This is 
	 * my solution to making the Tables available 
	 * 
	 * @param string $property
	 * @return Table|mixed
	 */
    public function __get($property) {
        if (in_array($property, ['Users', 'Members', 'Contacts', 'Addresses', 'Artists', 'GroupsMembers'])) {
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
		$schema->addColumn('user', ['type' => 'layer']);
		$schema->addColumn('member', ['type' => 'layer']);
		$schema->addColumn('contacts', ['type' => 'layer']);
		$schema->addColumn('addresses', ['type' => 'layer']);
		$schema->addColumn('artists', ['type' => 'layer']);
		$schema->addColumn('groupMembers', ['type' => 'layer']);
        return $schema;
    }
	
	/**
	 * The primary access point to get the UserStack
	 * 
	 * The user stack contains all the daily operating data for a single 
	 * register user. The user record in the stack contains a subset of 
	 * user record data omitting sensitive system access data.
	 * 
	 * The stack contains all the user's personal contact and address 
	 * data and group membership links they've established for their 
	 * personal member record.
	 * 
	 * It also contains thier artist records. These are join record that
	 * can lead to the member records that represent artist. These member 
	 * records may be the user's own records or those of another registered 
	 * system user that has give permission for artist management.
	 * 
	 * @param Query $query
	 * @param array $options None needed. Present for signature consistency
	 * @return UserStack
	 * @throws \BadMethodCallException
	 */
	public function findStack($query, $options) {
		$id = SystemState::userId();
		$userStack = $this->stackFromUserId($id);
		return $userStack;
    }
    
	/**
	 * Read the stack from cache or assemble it and cache it
	 * 
	 * This is an alternate finder for cases where you have a set 
	 * of Artworks id. 
	 * 
	 * @param array $ids Artwork ids
	 * @return StackSet
	 */
    private function stackFromUserId($id) {
		$t = CollectTimerMetrics::instance();
				
		$le = $t->startLogEntry("UserStack.$id");
		$stack = FALSE;
		$t->start("read", $le);
		$stack = Cache::read(
			cacheTools::key($id), 
			cacheTools::config()
			);
		$t->end('read', $le);

		if (!$stack) {
			$t->start("build", $le);
			$stack = new UserStack();

			$user = $this->Users->findById($id)
					->where(['active' => 1])
					->select(['username', 'email', 
						'first_name', 'last_name', 
						'activation_date', 'active', 
						'is_superuser', 'role', 
						'member_id', 'id'])
					;
			$stack = $this->_marshall($stack, 'user', $user->toArray());
			$member_id = $stack->primaryEntity()->member_id;

			$member = $this->Members->find('Members', ['values' => [$member_id]]);
			$stack = $this->_marshall($stack, 'member', $member->toArray());

			$contacts = $this->Contacts->find('inMembers', ['values' => [$member_id]]);
			$stack = $this->_marshall($stack, 'contacts', $contacts->toArray());

			$addresses = $this->Addresses->find('inMembers', ['values' => [$member_id]]);
			$stack = $this->_marshall($stack, 'addresses', $addresses->toArray());

			$artists = $this->Artists->find('inMembers', ['values' => [$id]]);
			$stack = $this->_marshall($stack, 'artists', $artists->toArray());

			$groupsMembers = $this->
				_loadFromJoinTable('GroupsMembers', 'member_id', [$member_id]);
			$stack = $this->_marshall(
					$stack, 
					'groupsMembers', 
					$groupsMembers->toArray());
			
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

		return $stack;
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

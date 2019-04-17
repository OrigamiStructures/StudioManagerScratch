<?php
namespace App\Model\Table;

use App\Model\Table\StacksTable;
use App\Model\Lib\Layer;
use Cake\Cache\Cache;
use App\Model\Lib\StackSet;
use App\Model\Entity\ArtStack;
use App\Cache\ArtStackCacheTools as cacheTools;
use App\SiteMetrics\CollectTimerMetrics;
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
class UserStacksTable extends StacksTable
{
    	
    /**
     * {@inheritdoc}
     */
    protected $stackSchema = 	[	
		['name' => 'user', 'specs' => ['type' => 'layer']],
		['name' => 'member', 'specs' => ['type' => 'layer']],
		['name' => 'contacts', 'specs' => ['type' => 'layer']],
		['name' => 'addresses', 'specs' => ['type' => 'layer']],
		['name' => 'artists', 'specs' => ['type' => 'layer']],
		['name' => 'groups_members', 'specs' => ['type' => 'layer']],
	];
    
    /**
     * {@inheritdoc}
     */
    protected $seedPoints = [
		'user', 'users',
	];
	
	/**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        $this->addLayerTable([
            'Users', 'Members', 'Contacts',
            'Addresses', 'Artists', 'GroupsMembers'
        ]);
        parent::initialize($config);
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
	protected function distillFromUser($ids) {
		return $this->stackFromUser($ids);
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
    private function stackFromUser($id) {
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
			$member_id = $stack->primaryEntity()->member_id;

			$member = $this->Members->find('Members', ['values' => [$member_id]]);
			$contacts = $this->Contacts->find('inMembers', ['values' => [$member_id]]);
			$addresses = $this->Addresses->find('inMembers', ['values' => [$member_id]]);
			$artists = $this->Artists->find('inMembers', ['values' => [$id]]);
			$groupsMembers = $this->
				_distillFromJoinTable('GroupsMembers', 'member_id', [$member_id]);
			
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
	
}

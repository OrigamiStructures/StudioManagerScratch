<?php
namespace App\Model\Table;

use App\Model\Table\StacksTable;
use App\Lib\Layer;
use App\SiteMetrics\CollectTimerMetrics;
use App\Model\Lib\StackSet;
use Cake\Cache\Cache;
use App\Cache\ArtStackCacheTools as cacheTools;

/**
 * CakePHP RolodexCardsTable
 * @author dondrake
 */
class RolodexCardsTable extends StacksTable {
	
    /**
     * {@inheritdoc}
     */
    protected $layerTables = ['Members', 'Contacts', 'Addresses', 'Groups', 'GroupsMembers'];
    
    /**
     * {@inheritdoc}
     */
    protected $stackSchema = 	[	
			['name' => 'member', 'specs' => ['type' => 'layer']],
            ['name' => 'contacts', 'specs' => ['type' => 'layer']],
            ['name' => 'addresses', 'specs' => ['type' => 'layer']],
            ['name' => 'member_of', 'specs' => ['type' => 'layer']],
			['name' => 'group', 'specs' => ['type' => 'layer']],
            ['name' => 'has_members', 'specs' => ['type' => 'layer']],
        ];
    
    /**
     * {@inheritdoc}
     */
    protected $seedPoints = [
			'member', 'members', 'contact', 'contacts', 'address', 
			'addresses', 'member_of', 'members_of', 'group', 'groups', 'has_members', 'has_member', 
		];

	
	/**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);
    }
	
// <editor-fold defaultstate="collapsed" desc="Concrete Start-from implementations">
	
	/**
	 * Load the artwork stacks to support these artworks
	 * 
	 * @param array $ids Artwork ids
	 * @return StackSet
	     */
	protected function loadFromMember($ids) {
		return $this->stacksFromMembers($ids);
	}

// </editor-fold>

	/**
	 * Read the stack from cache or assemble it and cache it
	 * 
	 * This is an alternate finder for cases where you have a set 
	 * of Members id. 
	 * 
	 * @param array $ids Member ids
	 * @return StackSet
	 */
    public function stacksFromMembers($ids) {
        if (!is_array($ids)) {
            $msg = "The ids must be provided as an array.";
            throw new \BadMethodCallException($msg);
        }
        
		$t = CollectTimerMetrics::instance();
		
        $this->stacks = new StackSet();
		
        foreach ($ids as $id) {
            $le = $t->startLogEntry("ArtStack.$id");
            $stack = FALSE;
            $t->start("read", $le);
            $stack = Cache::read(cacheTools::key($id), cacheTools::config());
            $t->end('read', $le);
            
            if (!$stack && !$this->stacks->isMember($id)) {
                $t->start("build", $le);
                $stack = $this->newEntity([]);
                
                $member = $this->Members->find('members', ['values' => [$id]]);
                    $stack->set(['member' => $member->toArray()]);
                
                if ($stack->count('member')) {
                    $contacts = $this->Contacts->find('inMembers', ['values' => [$id]]);
                    $addresses = $this->Addresses->find('inMembers', ['values' => [$id]]);
                    $group = $this->Groups->find('inMembers', ['values' => [$id]]);
                    $memberships = $this->GroupsMembers->find('inMembers', ['values' => [$id]]);
                    $group_members = $this->GroupsMembers->find('inGroups', ['values' => [$id]]);
					
                    $stack->set([
						'contacts' => $contacts->toArray(),
						'addresses' => $addresses->toArray(),
						'member_of' => $memberships->toArray(),
						'group' => $group->toArray(),
						'has_members' => $group_members->toArray(),
						]);
                }
				
				if ($stack->isGroup()) {
                    $group_members = $this->GroupsMembers->
							find('inGroups', ['values' => [$stack->getGroupId]]);
					$stack->set(['has_members' => $group_members->toArray()]);
				}
                
                $t->end('build', $le);
                $t->start("write", $le);
//                Cache::write(cacheTools::key($id), $stack, cacheTools::config());
                $t->end('write', $le);
            }
        
            $t->logTimers($le);
            
            if ($stack->count('member')) {
                $stack->clean();
                $this->stacks->insert($id, $stack);
            }            
        }
			
        return $this->stacks;
    }
	
}

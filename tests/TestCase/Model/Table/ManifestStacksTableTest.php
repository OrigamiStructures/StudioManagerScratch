<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ManifestStacksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use App\Model\Lib\CurrentUser;

/**
 * App\Model\Table\ManifestStacksTable Test Case
 */
class ManifestStacksTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ManifestStacksTable
     */
    public $ManifestStacksTable;

	public $ManifestStacks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.manifests',
		// to support PersonCard inclusion in object
        'app.identities',
        'app.data_owners',
        'app.members',
        'app.contacts',
		'app.addresses',
		'app.dispositions',
		'app.users',
		'app.groups_members',
		// end person card stuff
		'app.permissions'
    ];

	protected $user = 	  [
	 	'id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
	 	'management_token' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
	 	'username' => 'don',
	 	'email' => 'ddrake@dreamingmind.com',
	 	'first_name' => 'Don',
	 	'last_name' => 'Drake',
	 	'active' => true,
	 	'is_superuser' => false,
	 	'role' => 'user',
	 	'artist_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
	 	'member_id' => 1
	  ];


    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ManifestStacks') ? [] : ['className' => ManifestStacksTable::class];
        $this->ManifestStacksTable = TableRegistry::getTableLocator()->get('ManifestStacks', $config);
		$this->ManifestStacksTable->setCurrentUser(new CurrentUser($this->user));
		$this->ManifestStacksTable->setContextUser(new CurrentUser($this->user));
		$this->ManifestStacks = $this->ManifestStacksTable
				->find('stacksFor', ['seed' => 'manifests', 'ids' => [1,2,3,4,5]]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ManifestStacksTable);

        parent::tearDown();
    }

    /**
     * Test findSupervisorManifests method
     *
     * @return void
     */
    public function testFindSupervisorManifestsWithIds()
    {
        $options = ['ids' => [
			'708cfc57-1162-4c5b-9092-42c25da131a9', 
			'f22f9b46-345f-4c6f-9637-060ceacb21b2'
		]];
		$manifests = $this->ManifestStacksTable->find('supervisorManifests', $options);
		
		$this->assertTrue($manifests instanceof \App\Model\Lib\StackSet, 
				'find with currentUser option did not return StackSet');
		$this->assertCount(5, $manifests->load(), 'find with currentUser option did '
				. 'not return expected number of results');
    }
	
	public function testFindSupervisorManifestWithCurrentUser() {
        $options = ['source' => 'currentUser'];
		$manifests = $this->ManifestStacksTable->find('supervisorManifests', $options);
		
		$this->assertTrue($manifests instanceof \App\Model\Lib\StackSet, 
				'find with id array option did not return StackSet');
		$this->assertCount(3, $manifests->load(), 'find with id array option did '
				. 'not return expected number of results');
	}
	
	public function testFindSupervisorManifestWithContextUser() {
        $options = ['source' => 'contextUser'];
		$manifests = $this->ManifestStacksTable->find('supervisorManifests', $options);
		
		$this->assertTrue($manifests instanceof \App\Model\Lib\StackSet, 
				'find with contextUser option did not return StackSet');
		$this->assertCount(3, $manifests->load(), 'find with contextUser option did '
				. 'not return expected number of results');
	}
	
	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testFindSupervisorManifestWithBadArgs() {
        $options = ['bad' => 'key'];
		$manifests = $this->ManifestStacksTable->find('supervisorManifests', $options);
	}
	
}

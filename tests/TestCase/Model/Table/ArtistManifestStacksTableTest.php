<?php
//namespace App\Test\TestCase\Model\Table;
//
//use App\Model\Table\ArtistManifestStacksTable;
//use Cake\ORM\TableRegistry;
//use Cake\TestSuite\TestCase;
//use App\Model\Lib\CurrentUser;
//
///**
// * App\Model\Table\ManifestStacksTable Test Case
// */
//class ManifestStacksTableTest extends TestCase
//{
//
//    /**
//     * Test subject
//     *
//     * @var \App\Model\Table\ArtistManifestStacksTable
//     */
//    public $ArtistManifestStacksTable;
//
//	public $ArtistManifestStacks;
//
//    /**
//     * Fixtures
//     *
//     * @var array
//     */
//    public $fixtures = [
//        'app.manifests',
//		// to support PersonCard inclusion in object
//        'app.identities',
//        'app.data_owners',
//        'app.members',
//        'app.contacts',
//		'app.addresses',
//		'app.dispositions',
//		'app.users',
//		'app.groups_members',
//		// end person card stuff
//		'app.permissions'
//    ];
//
//	protected $user = 	  [
//		[
//			'id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
//			'management_token' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
//			'username' => 'don',
//			'email' => 'ddrake@dreamingmind.com',
//			'first_name' => 'Don',
//			'last_name' => 'Drake',
//			'active' => true,
//			'is_superuser' => false,
//			'role' => 'user',
//			'artist_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
//			'member_id' => 1			
//		],
//		[
//			'id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
//			'management_token' => '708cfc57-1162-4c5b-9092-42c25da131a9',
//			'username' => 'leonardo',
//			'email' => 'horseman@dreamingmind.com',
//			'first_name' => 'Luis',
//			'last_name' => 'Delgado',
//			'active' => true,
//			'is_superuser' => false,
//			'role' => 'user',
//			'artist_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
//			'member_id' => 75			
//		],
//	];
//
//
//    /**
//     * setUp method
//     *
//     * @return void
//     */
//    public function setUp()
//    {
//		
//        parent::setUp();
//        $config = TableRegistry::getTableLocator()->exists('ManifestStacks') ? [] : ['className' => ArtistManifestStacksTable::class];
//        $this->ArtistManifestStacksTable = TableRegistry::getTableLocator()->get('ManagerManifestStacks', $config);
//		$this->ArtistManifestStacksTable->setCurrentUser(new CurrentUser($this->user[0]));
//		$this->AtristManifestStacksTable->contextUser = $this->setupContextUser();
//		$this->ArtistManifestStacks = $this->ArtistManifestStacksTable
//				->find('stacksFor', ['seed' => 'manifests', 'ids' => [1,2,3,4,5]]);
//    }
//
//	
//	public function setupContextUser() {
//		$this->Session = $this->createMock(\Cake\Http\Session::class);
//		$this->Session->method('read')->will($this->onConsecutiveCalls($this->user[1], NULL));
//		return ContextUser::instance(['session' => $this->Session]);
//	}
//
//    /**
//     * tearDown method
//     *
//     * @return void
//     */
//    public function tearDown()
//    {
//        unset($this->ArtistManifestStacksTable);
//
//        parent::tearDown();
//    }
//
//    /**
//     * Test findSupervisorManifests method
//     *
//     * @return void
//     */
//    public function testFindSupervisorManifestsWithIds()
//    {
//        $options = ['ids' => [
//			'708cfc57-1162-4c5b-9092-42c25da131a9', 
//			'f22f9b46-345f-4c6f-9637-060ceacb21b2'
//		]];
//		$manifests = $this->ArtistManifestStacksTable->find('supervisorManifests', $options);
//		
//		$this->assertTrue($manifests instanceof \App\Model\Lib\StackSet, 
//				'find with currentUser option did not return StackSet');
//		$this->assertCount(3, $manifests->load(), 'find with currentUser option did '
//				. 'not return expected number of results');
//    }
//	
//	public function testFindSupervisorManifestWithCurrentUser() {
//        $options = ['source' => 'currentUser'];
//		$manifests = $this->ArtistManifestStacksTable->find('supervisorManifests', $options);
//		
//		$this->assertTrue($manifests instanceof \App\Model\Lib\StackSet, 
//				'find with id array option did not return StackSet');
//		$this->assertCount(3, $manifests->load(), 'find with id array option did '
//				. 'not return expected number of results');
//	}
//	
//	public function testFindSupervisorManifestWithContextUser() {
//        $options = ['source' => 'contextUser'];
//		$manifests = $this->ArtistManifestStacksTable->find('supervisorManifests', $options);
//		
//		$this->assertTrue($manifests instanceof \App\Model\Lib\StackSet, 
//				'find with contextUser option did not return StackSet');
//		$this->assertCount(0, $manifests->load(), 'find with contextUser option did '
//				. 'not return expected number of results');
//	}
//	
//	/**
//	 * @expectedException \BadMethodCallException
//	 */
//	public function testFindSupervisorManifestWithBadArgs() {
//        $options = ['bad' => 'key'];
//		$manifests = $this->ArtistManifestStacksTable->find('supervisorManifests', $options);
//	}
//	
//}

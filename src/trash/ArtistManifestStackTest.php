<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\ArtistManifestStack;
use App\Model\Table\ArtistManifestStacksTable;
use Cake\TestSuite\TestCase;
use App\Model\Lib\CurrentUser;
use Cake\ORM\TableRegistry;
use App\Model\Lib\ContextUser;

/**
 * App\Model\Entity\ManifestStack Test Case
 */
class ArtistManifestStackTest extends TestCase
{

    public $AtristManifestStacksTable;

	public $ArtistManifestStacks;

	public $selfManage;
	
	public $otherManage;
	
	public $foreignManage;
	
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
        'app.images',
		'app.addresses',
		'app.dispositions',
		'app.users',
		'app.groups_members',
		// end person card stuff
		'app.permissions'
    ];

	protected $user = 	  [
		[
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
		],
		[
			'id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
			'management_token' => '708cfc57-1162-4c5b-9092-42c25da131a9',
			'username' => 'leonardo',
			'email' => 'horseman@dreamingmind.com',
			'first_name' => 'Luis',
			'last_name' => 'Delgado',
			'active' => true,
			'is_superuser' => false,
			'role' => 'user',
			'artist_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
			'member_id' => 75			
		],
	];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
//		$supervisor = $this->createMock(\App\Model\Entity\PersonCard::class);
//        $config = TableRegistry::getTableLocator()
//				->exists('ManifestStacks') ? [] : ['className' => ArtistManifestStacksTable::class];
//        $this->AtristManifestStacksTable = TableRegistry::getTableLocator()->get('ArtistManifestStacks', $config);
//		$this->AtristManifestStacksTable->setCurrentUser(new CurrentUser($this->user[0]));
////		$this->AtristManifestStacksTable->setContextUser(new CurrentUser($this->user[1]));
//		$this->AtristManifestStacksTable->contextUser = $this->setupContextUser();
//		$this->ArtistManifestStacks = $this->AtristManifestStacksTable
//				->find('stacksFor', ['seed' => 'manifests', 'ids' => [1,2,3]]);
//		//Supervisor manages self/artist
//		$this->selfManage = $this->ArtistManifestStacks->element(1, LAYERACC_ID);
////		debug($this->ArtistManifestStacks);die;
//		//Supervisor/manager manages another artist
//		$this->otherManage = $this->ArtistManifestStacks->element(2, LAYERACC_ID);
//		//Foreign manager manages Supervisor's artist
//		$this->foreignManage = $this->ArtistManifestStacks->element(3, LAYERACC_ID);
////		debug($this->otherManage);
//    }
//	
//	public function setupContextUser() {
//		$this->Session = $this->createMock(\Cake\Http\Session::class);
//		$this->Session->method('read')->will($this->onConsecutiveCalls($this->user[1], NULL));
//		return ContextUser::instance(['session' => $this->Session]);
	}

//    /**
//     * tearDown method
//     *
//     * @return void
//     */
//    public function tearDown()
//    {
//        unset($this->ArtistManifestStacks);
//		unset($this->AtristManifestStacksTable);
//		unset($this->selfManage);
//		unset($this->otherManage);
//		unset($this->foreignManage);
//
//        parent::tearDown();
//    }
//
    /**
     * Test manifest method
     *
     * @return void
     */
    public function testManifest()
    {
		$this->markTestIncomplete();
//        $this->assertTrue($this->selfManage->manifest() instanceof \App\Model\Entity\Manifest,
//				'manifest() didn\'t return a ManifestEntity');
    }
//
//    /**
//     * Test supervisorCard method
//     *
//     * @return void
//     */
//    public function testSupervisorCard()
//    {
//		$result = $this->selfManage->supervisorCard();
//        $this->assertTrue($result instanceof \App\Model\Entity\PersonCard);
//    }
//
//    /**
//     * Test managerCard method
//     *
//     * @return void
//     */
//    public function testManagerCard()
//    {
//        $this->markTestIncomplete('Not implemented yet.');
//    }
//
//    /**
//     * Test artistCard method
//     *
//     * @return void
//     */
//    public function testArtistCard()
//    {
//        $this->markTestIncomplete('Not implemented yet.');
//    }
//
//    /**
//     * Test selfAssigned method
//     *
//     * @return void
//     */
//    public function testSelfAssigned()
//    {
//        $this->markTestIncomplete('Not implemented yet.');
//    }
//
//    /**
//     * Test accessSummary method
//     *
//     * @return void
//     */
//    public function testAccessSummary()
//    {
//        $this->markTestIncomplete('Not implemented yet.');
//    }
}

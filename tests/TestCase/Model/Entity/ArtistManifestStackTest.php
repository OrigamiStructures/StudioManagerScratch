<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\ArtistManifestStack;
use App\Model\Table\ArtistManifestStacksTable;
use Cake\TestSuite\TestCase;
use App\Model\Lib\CurrentUser;
use Cake\ORM\TableRegistry;

/**
 * App\Model\Entity\ManifestStack Test Case
 */
class ArtistManifestStackTest extends TestCase
{

    public $ManifestStacksTable;

	public $ManifestStacks;

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
     * Test subject
     *
     * @var \App\Model\Entity\ArtistManifestStack
     */
    public $ManifestStack;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
		$supervisor = $this->createMock(\App\Model\Entity\PersonCard::class);
        $config = TableRegistry::getTableLocator()
				->exists('ManifestStacks') ? [] : ['className' => ArtistManifestStacksTable::class];
        $this->ManifestStacksTable = TableRegistry::getTableLocator()->get('ManifestStacks', $config);
		$this->ManifestStacksTable->setCurrentUser(new CurrentUser($this->user[0]));
		$this->ManifestStacksTable->setContextUser(new CurrentUser($this->user[1]));
		$this->ManifestStacks = $this->ManifestStacksTable
				->find('stacksFor', ['seed' => 'manifests', 'ids' => [1,2,3]]);
		//Supervisor manages self/artist
		$this->selfManage = $this->ManifestStacks->element(1, LAYERACC_ID);
		//Supervisor/manager manages another artist
		$this->otherManage = $this->ManifestStacks->element(2, LAYERACC_ID);
		//Foreign manager manages Supervisor's artist
		$this->foreignManage = $this->ManifestStacks->element(3, LAYERACC_ID);
//		debug($this->otherManage);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ManifestStacks);
		unset($this->ManifestStacksTable);
		unset($this->selfManage);
		unset($this->otherManage);
		unset($this->foreignManage);

        parent::tearDown();
    }

    /**
     * Test manifest method
     *
     * @return void
     */
    public function testManifest()
    {
        $this->assertTrue($this->selfManage->manifest() instanceof \App\Model\Entity\Manifest,
				'manifest() didn\'t return a ManifestEntity');
    }

    /**
     * Test supervisorCard method
     *
     * @return void
     */
    public function testSupervisorCard()
    {
		$result = $this->selfManage->supervisorCard();
        $this->assertTrue($result instanceof \App\Model\Entity\PersonCard);
    }

    /**
     * Test managerCard method
     *
     * @return void
     */
    public function testManagerCard()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test artistCard method
     *
     * @return void
     */
    public function testArtistCard()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test selfAssigned method
     *
     * @return void
     */
    public function testSelfAssigned()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test accessSummary method
     *
     * @return void
     */
    public function testAccessSummary()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ManifestStacksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

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
        'app.identities',
        'app.data_owners',
        'app.members',
        'app.contacts',
		'app.addresses',
		'app.dispositions',
		'app.users',
		'app.groups_members'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()
				->exists('ManifestStacks') ? [] : ['className' => ManifestStacksTable::class];
        $this->ManifestStacksTable = TableRegistry::getTableLocator()->get('ManifestStacks', $config);
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
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findManifests method
     *
     * @return void
     */
    public function testFindManifests()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

}

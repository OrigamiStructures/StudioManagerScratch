<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ManifestsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ManifestsTable Test Case
 */
class ManifestsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ManifestsTable
     */
    public $Manifests;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.manifests',
        'app.members',
        'app.users',
        'app.member_users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Manifests') ? [] : ['className' => ManifestsTable::class];
        $this->Manifests = TableRegistry::getTableLocator()->get('Manifests', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Manifests);

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
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findArtists method
     *
     * @return void
     */
    public function testFindArtists()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findInMembers method
     *
     * @return void
     */
    public function testFindInMembers()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findManagedBy method
     *
     * @return void
     */
    public function testFindManagedBy()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findIssuedBy method
     *
     * @return void
     */
    public function testFindIssuedBy()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findManifestsFor method
     *
     * @return void
     */
    public function testFindManifestsFor()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

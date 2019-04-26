<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ArtistManifestsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ArtistManifestsTable Test Case
 */
class ArtistManifestsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ArtistManifestsTable
     */
    public $ArtistManifestsTable;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ArtistManifests') ? [] : ['className' => ArtistManifestsTable::class];
        $this->ArtistManifestsTable = TableRegistry::getTableLocator()->get('ArtistManifests', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ArtistManifestsTable);

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
     * Test distillFromManager method
     *
     * @return void
     */
    public function testDistillFromManager()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test distillFromDataOwner method
     *
     * @return void
     */
    public function testDistillFromDataOwner()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test distillFromManifest method
     *
     * @return void
     */
    public function testDistillFromManifest()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test distillFromIdentity method
     *
     * @return void
     */
    public function testDistillFromIdentity()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test distillFromPermission method
     *
     * @return void
     */
    public function testDistillFromPermission()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test marshalIdentity method
     *
     * @return void
     */
    public function testMarshalIdentity()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test marshalManifests method
     *
     * @return void
     */
    public function testMarshalManifests()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test marshalDataOwner method
     *
     * @return void
     */
    public function testMarshalDataOwner()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test marshalManagers method
     *
     * @return void
     */
    public function testMarshalManagers()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test marshalPermissions method
     *
     * @return void
     */
    public function testMarshalPermissions()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

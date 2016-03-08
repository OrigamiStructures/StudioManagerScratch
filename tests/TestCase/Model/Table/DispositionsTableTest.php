<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DispositionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DispositionsTable Test Case
 */
class DispositionsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\DispositionsTable
     */
    public $Dispositions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.dispositions',
        'app.users',
        'app.members',
        'app.images',
        'app.artworks',
        'app.editions',
        'app.series',
        'app.formats',
        'app.subscriptions',
        'app.pieces',
        'app.dispositions_pieces',
        'app.locations',
        'app.addresses',
        'app.contacts',
        'app.groups',
        'app.groups_members',
        'app.proxy_members',
        'app.proxy_groups'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Dispositions') ? [] : ['className' => 'App\Model\Table\DispositionsTable'];
        $this->Dispositions = TableRegistry::get('Dispositions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Dispositions);

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
     * Test map method
     *
     * @return void
     */
    public function testMap()
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
     * Test validLabel method
     *
     * @return void
     */
    public function testValidLabel()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test endOfLoan method
     *
     * @return void
     */
    public function testEndOfLoan()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test beforeMarshal method
     *
     * @return void
     */
    public function testBeforeMarshal()
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
     * Test markCollected method
     *
     * @return void
     */
    public function testMarkCollected()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

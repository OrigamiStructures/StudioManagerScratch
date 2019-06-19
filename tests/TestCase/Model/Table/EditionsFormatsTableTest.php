<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\EditionsFormatsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\EditionsFormatsTable Test Case
 */
class EditionsFormatsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\EditionsFormatsTable
     */
    public $EditionsFormats;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.editions_formats',
        'app.users',
        'app.formats',
        'app.editions'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('EditionsFormats') ? [] : ['className' => EditionsFormatsTable::class];
        $this->EditionsFormats = TableRegistry::getTableLocator()->get('EditionsFormats', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EditionsFormats);

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
}

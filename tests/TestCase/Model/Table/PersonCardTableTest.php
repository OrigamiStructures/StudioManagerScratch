<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PersonCardTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PersonCardTable Test Case
 */
class PersonCardTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PersonCardTable
     */
    public $PersonCardTable;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('PersonCard') ? [] : ['className' => PersonCardTable::class];
        $this->PersonCardTable = TableRegistry::getTableLocator()->get('PersonCard', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PersonCardTable);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

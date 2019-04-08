<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CategoryCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CategoryCardsTable Test Case
 */
class CategoryCardsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CategoryCardsTable
     */
    public $CategoryCardsTable;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.category_cards',
        'app.data_owners',
        'app.memberships'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CategoryCards') ? [] : ['className' => CategoryCardsTable::class];
        $this->CategoryCardsTable = TableRegistry::getTableLocator()->get('CategoryCards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CategoryCardsTable);

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
}

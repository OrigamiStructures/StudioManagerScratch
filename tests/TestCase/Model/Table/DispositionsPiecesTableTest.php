<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DispositionsPiecesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DispositionsPiecesTable Test Case
 */
class DispositionsPiecesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\DispositionsPiecesTable
     */
    public $DispositionsPieces;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('DispositionsPieces') ? [] : ['className' => DispositionsPiecesTable::class];
        $this->DispositionsPieces = TableRegistry::getTableLocator()->get('DispositionsPieces', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DispositionsPieces);

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

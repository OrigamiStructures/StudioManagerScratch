<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FormatsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FormatsTable Test Case
 */
class FormatsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\FormatsTable
     */
    public $Formats;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.formats',
        'app.users',
        'app.images',
        'app.editions',
        'app.subscriptions',
        'app.pieces'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Formats') ? [] : ['className' => FormatsTable::class];
        $this->Formats = TableRegistry::getTableLocator()->get('Formats', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Formats);

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
     * Test afterSave method
     *
     * @return void
     */
    public function testAfterSave()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findChoiceList method
     *
     * @return void
     */
    public function testFindChoiceList()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findSoldOut method
     *
     * @return void
     */
    public function testFindSoldOut()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findCollectedPieceCount method
     *
     * @return void
     */
    public function testFindCollectedPieceCount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findCollectedPiecePercentage method
     *
     * @return void
     */
    public function testFindCollectedPiecePercentage()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test inSubscription method
     *
     * @return void
     */
    public function testInSubscription()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findHasFluid method
     *
     * @return void
     */
    public function testFindHasFluid()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findEmpty method
     *
     * @return void
     */
    public function testFindEmpty()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findSearch method
     *
     * @return void
     */
    public function testFindSearch()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test clearCache method
     *
     * @return void
     */
    public function testClearCache()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test readCache method
     *
     * @return void
     */
    public function testReadCache()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test writeCache method
     *
     * @return void
     */
    public function testWriteCache()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

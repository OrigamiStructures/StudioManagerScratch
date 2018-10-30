<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PiecesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PiecesTable Test Case
 */
class PiecesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PiecesTable
     */
    public $Pieces;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.pieces',
        'app.users',
        'app.editions',
        'app.formats',
        'app.dispositions'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Pieces') ? [] : ['className' => PiecesTable::class];
        $this->Pieces = TableRegistry::getTableLocator()->get('Pieces', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Pieces);

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
     * Test implementedEvents method
     *
     * @return void
     */
    public function testImplementedEvents()
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
     * Test beforeFind method
     *
     * @return void
     */
    public function testBeforeFind()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test formatsAssignedPieceCount method
     *
     * @return void
     */
    public function testFormatsAssignedPieceCount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test editionsAssignedPieceCount method
     *
     * @return void
     */
    public function testEditionsAssignedPieceCount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test formatsFluidPieceCount method
     *
     * @return void
     */
    public function testFormatsFluidPieceCount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test editionsFluidPieceCount method
     *
     * @return void
     */
    public function testEditionsFluidPieceCount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findDispositionCount method
     *
     * @return void
     */
    public function testFindDispositionCount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findQuantity method
     *
     * @return void
     */
    public function testFindQuantity()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findAssignedTo method
     *
     * @return void
     */
    public function testFindAssignedTo()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findInEdition method
     *
     * @return void
     */
    public function testFindInEdition()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findIsDisposed method
     *
     * @return void
     */
    public function testFindIsDisposed()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findNotDisposed method
     *
     * @return void
     */
    public function testFindNotDisposed()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findFluid method
     *
     * @return void
     */
    public function testFindFluid()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findAssigned method
     *
     * @return void
     */
    public function testFindAssigned()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findIsCollected method
     *
     * @return void
     */
    public function testFindIsCollected()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findNotCollected method
     *
     * @return void
     */
    public function testFindNotCollected()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findNumbers method
     *
     * @return void
     */
    public function testFindNumbers()
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
     * Test findCanDispose method
     *
     * @return void
     */
    public function testFindCanDispose()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test highestNumberDisposed method
     *
     * @return void
     */
    public function testHighestNumberDisposed()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test spawn method
     *
     * @return void
     */
    public function testSpawn()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test splitPiece method
     *
     * @return void
     */
    public function testSplitPiece()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test stack method
     *
     * @return void
     */
    public function testStack()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test merge method
     *
     * @return void
     */
    public function testMerge()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test persistAll method
     *
     * @return void
     */
    public function testPersistAll()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test save method
     *
     * @return void
     */
    public function testSave()
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

<?php
namespace App\Test\TestCase\Model\Lib;

use App\Model\Lib\StackSet;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Lib\StackSet Test Case
 */
class StackSetTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.art_stack_pieces',
        'app.art_stack_artworks',
    ];

    /**
     * Test subject
     *
     * @var \App\Model\Lib\StackSet
     */
    public $StackSet;

    public $Pieces;
    public $Artworks;
    public $pieceRecords;
    public $artRecord;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Pieces = $this->getTableLocator()->get('Pieces');
        $this->pieceRecords = $this->Pieces->find('all')->toArray();
        $this->Artworks = $this->getTableLocator()->get('Artworks');
        $this->artRecord = $this->Artworks->find('all')->toArray();
        
        $chunks = array_chunk($this->pieceRecords, 5);
        $this->fivePieces = $chunks[1];

    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Pieces);
        unset($this->pieceRecords);
        unset($this->Artworks);
        unset($this->artRecord);
        unset($this->fivePieces);

        parent::tearDown();
    }

    /**
     * Test insert method
     *
     * @return void
     */
    public function testInsert()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test all method
     *
     * @return void
     */
    public function testAll()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test members method
     *
     * @return void
     */
    public function testMembers()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test element method
     *
     * @return void
     */
    public function testElement()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test member method
     *
     * @return void
     */
    public function testMember()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test count method
     *
     * @return void
     */
    public function testCount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isMember method
     *
     * @return void
     */
    public function testIsMember()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test load method
     *
     * @return void
     */
    public function testLoad()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test ownerOf method
     *
     * @return void
     */
    public function testOwnerOf()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test IDs method
     *
     * @return void
     */
    public function testIDs()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

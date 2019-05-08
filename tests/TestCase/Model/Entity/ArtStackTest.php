<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\ArtStack;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\ArtStack Test Case
 */
class ArtStackTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\ArtStack
     */
    public $ArtStack;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ArtStack = new ArtStack();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ArtStack);

        parent::tearDown();
    }

    /**
     * Test emitEditionStack method
     *
     * @return void
     */
    public function testEmitEditionStack()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test emitFormatStack method
     *
     * @return void
     */
    public function testEmitFormatStack()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test emitPieceStack method
     *
     * @return void
     */
    public function testEmitPieceStack()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

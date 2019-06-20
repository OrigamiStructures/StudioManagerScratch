<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\ManifestStack;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\ManifestStack Test Case
 */
class ManifestStackTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\ManifestStack
     */
    public $ManifestStack;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ManifestStack = new ManifestStack();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ManifestStack);

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

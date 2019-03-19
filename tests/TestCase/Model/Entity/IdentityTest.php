<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Identity;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Identity Test Case
 */
class IdentityTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\Identity
     */
    public $Identity;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Identity = new Identity();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Identity);

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

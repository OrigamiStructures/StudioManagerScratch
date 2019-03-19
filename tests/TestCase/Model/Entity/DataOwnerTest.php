<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\DataOwner;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\DataOwner Test Case
 */
class DataOwnerTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\DataOwner
     */
    public $DataOwner;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->DataOwner = new DataOwner();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DataOwner);

        parent::tearDown();
    }

    /**
     * Test id method
     *
     * @return void
     */
    public function testId()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test userId method
     *
     * @return void
     */
    public function testUserId()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

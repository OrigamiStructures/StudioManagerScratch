<?php
namespace App\Test\TestCase\Model\Behavior;

use App\Model\Behavior\IntegerQueryBehavior;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Behavior\IntegerQueryBehavior Test Case
 */
class IntegerQueryBehaviorTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Behavior\IntegerQueryBehavior
     */
    public $IntegerQuery;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->IntegerQuery = new IntegerQueryBehavior();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->IntegerQuery);

        parent::tearDown();
    }

    /**
     * Test integer method
     *
     * @return void
     */
    public function testInteger()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

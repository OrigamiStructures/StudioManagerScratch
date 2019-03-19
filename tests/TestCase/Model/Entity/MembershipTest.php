<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Membership;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Membership Test Case
 */
class MembershipTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\Membership
     */
    public $Membership;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Membership = new Membership();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Membership);

        parent::tearDown();
    }

    /**
     * Test groupId method
     *
     * @return void
     */
    public function testGroupId()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test groupIsActive method
     *
     * @return void
     */
    public function testGroupIsActive()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Member;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Member Test Case
 */
class MemberTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\Member
     */
    public $Member;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Member = new Member();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Member);

        parent::tearDown();
    }

    /**
     * Test _name method
     *
     * @return void
     */
    public function testName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test _reverseName method
     *
     * @return void
     */
    public function testReverseName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test name method
     *
     * @return void
     */
    public function testName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isCollector method
     *
     * @return void
     */
    public function testIsCollector()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test collectedCount method
     *
     * @return void
     */
    public function testCollectedCount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isDispositionParticipant method
     *
     * @return void
     */
    public function testIsDispositionParticipant()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test dispositionCount method
     *
     * @return void
     */
    public function testDispositionCount()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isActive method
     *
     * @return void
     */
    public function testIsActive()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test firstName method
     *
     * @return void
     */
    public function testFirstName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test lastName method
     *
     * @return void
     */
    public function testLastName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test type method
     *
     * @return void
     */
    public function testType()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

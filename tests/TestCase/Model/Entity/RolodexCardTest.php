<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\RolodexCard;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\RolodexCard Test Case
 */
class RolodexCardTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\RolodexCard
     */
    public $RolodexCard;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->RolodexCard = new RolodexCard();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RolodexCard);

        parent::tearDown();
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
     * Test isMember method
     *
     * @return void
     */
    public function testIsMember()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test memberships method
     *
     * @return void
     */
    public function testMemberships()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

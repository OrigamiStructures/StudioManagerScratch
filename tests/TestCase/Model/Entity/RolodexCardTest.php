<?php

namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\RolodexCard;
use App\Model\Lib\Layer;
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
    public $Person;
    public $Groups;
    public $RolodexCards;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.members',
        'app.users',
        'app.groups_members',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->RolodexCards = $this->getTableLocator()->get('RolodexCards');

        $targets = ['seed' => 'identity', 'ids' => [2, 3]];
        $cards = $this->RolodexCards->find('stacksFor', $targets);

        $this->Person = $cards->element(0);
        $this->Group = $cards->element(1);

    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset(
            $this->Person,
            $this->Group,
            $this->RolodexCards);

        parent::tearDown();
    }

    /**
     * Test name method
     *
     * @return void
     */
    public function testName()
    {
        $this->assertEquals('Gail Drake', $this->Person->name(),
            'The person card\'s name() passthrough did not work');

        $this->assertEquals('Drake Family', $this->Group->name(),
            'The group card\'s name() passthrough did not work');

    }

    /**
     * Test isMember method
     *
     * @return void
     */
    public function testIsMember()
    {
        $this->assertTrue($this->Person->isMember(),
            'The person card\'s isMember() check did not work');

        $this->assertFalse($this->Group->isMember(),
            'The group card\'s isMember() check did not work');

    }

    /**
     * Test membershipElements method
     *
     * @return void
     */
    public function testGetMemberships()
    {
        $this->assertCount(2, $this->Person->getMemberships(),
            'The person card\'s membershipElements() accessor did not work');

        $this->assertCount(0, $this->Group->getMemberships(),
            'The group card\'s membershipElements() accessor did not work');

        $this->assertInstanceOf(Layer::class, $this->Person->getMemberships(),
            'Requesting a layer did not return one.');

        $this->assertInstanceOf(Layer::class, $this->Group->getMemberships(),
            'Requesting a layer form empty memberships did not return one.');

    }

}

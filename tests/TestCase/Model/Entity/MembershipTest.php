<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Membership;
use App\Model\Table\MembershipsTable;
use Cake\ORM\TableRegistry;
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

    /* Test subject
     *
     * @var \App\Model\Table\MembershipsTable
     */
    public $Memberships;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.groups',
        'app.members',
//        'app.group_members'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Memberships') ? [] : ['className' => MembershipsTable::class];
        $this->Memberships = TableRegistry::getTableLocator()->get('Memberships', $config);
        $this->Membership = $this->Memberships->find('hook');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Memberships);
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
        $this->assertEquals(1, $this->Membership[0]->groupId(),
            'No group id or the wrong id was migrated from the '
            . 'group record onto the groupIdentity record');
        $this->assertEquals(4, $this->Membership[3]->groupId(),
            'No group id or the wrong id was migrated from the '
            . 'group record onto the groupIdentity record');
    }

    /**
     * Test groupIsActive method
     *
     * @return void
     */
    public function testGroupIsActive()
    {
        $this->assertEquals(TRUE, $this->Membership[0]->isActive(),
            'No active value or the wrong value was migrated from the '
            . 'group record onto the groupIdentity record');
    }
    
    /**
     * Test firstName method
     *
     * @return void
     */
    public function testFirstNamePassthrough()
    {
        $this->assertEquals('', $this->Membership[0]->firstName(),
            'A Passthrough method (firstName) into Members etity failed.');
    }

    /**
     * Test lastName method
     *
     * @return void
     */
    public function testLastNamePassthrough()
    {
        $this->assertEquals('Drake Family', $this->Membership[0]->lastName(),
            'A Passthrough method (lastName) into Members etity failed.');
    }

    /**
     * Test type method
     *
     * @return void
     */
    public function testTypePassthrough()
    {
        $this->assertEquals('Group', $this->Membership[0]->type(),
            'A Passthrough method (type) into Members etity failed.');
    }
}

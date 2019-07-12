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
        'app.members',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()
            ->exists('Memberships') ? [] : ['className' => MembershipsTable::class];
        $this->Memberships = TableRegistry::getTableLocator()
            ->get('Memberships', $config);
        $this->Membership = $this
            ->Memberships
            ->find('hook')
//            ->where(['type IN' => ['Institution', 'Organization', 'Category']])
            ->toArray();
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
//        pr($this->Membership);
        $this->assertEquals('', $this->Membership[2]->firstName(),
            'A Passthrough method (firstName) into Members etity failed.');
    }

    /**
     * Test lastName method
     *
     * @return void
     */
    public function testLastNamePassthrough()
    {
        $this->assertEquals('Drake Family', $this->Membership[2]->lastName(),
            'A Passthrough method (lastName) into Members etity failed.');
    }

    /**
     * Test type method
     *
     * @return void
     */
    public function testTypePassthrough()
    {
        $this->assertEquals('Category', $this->Membership[6]->type(),
            'A Passthrough method (type) into Members etity failed.');
    }
}

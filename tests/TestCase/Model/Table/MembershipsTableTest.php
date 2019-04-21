<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MembershipsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MembershipsTable Test Case
 */
class MembershipsTableTest extends TestCase
{

    /**
     * Test subject
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
//        'app.groups',
        'app.members',
        'app.groups_members'
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
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Memberships);

        parent::tearDown();
    }

    /**
     * Test initializeAssociations method
     *
     * @return void
     */
    public function testInitializeAssociations()
    {
        $this->assertTrue(
            is_a($this->Memberships->GroupIdentities, 'Cake\ORM\Association\BelongsToMany'),
            'The GroupIdentities belongsTo association was not made.');
    }

    /**
     * Test findHook method
     *
     * @return void
     */
    public function testFindHook()
    {
        $groupHooks = $this->Memberships->find('hook')->toArray();
		// DESIRED BEHAVIOR?
//        $this->assertTrue(is_a($groupHooks[0], 'App\Model\Entity\GroupMembers'),
//            'hook result entity is not a GroupsMember entity.');
        $this->assertTrue(is_a($groupHooks[0], 'App\Model\Entity\Member'),
            'hook result entity does not extend Member entity');
    }
}

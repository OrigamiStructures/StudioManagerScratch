<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GroupsMembersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GroupsMembersTable Test Case
 */
class GroupsMembersTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.groups_members',
        'app.users',
        'app.members',
        'app.dispositions',
        'app.locations',
        'app.pieces',
        'app.editions',
        'app.artworks',
        'app.formats',
//        'app.groups'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('GroupsMembers') ? [] : ['className' => 'App\Model\Table\GroupsMembersTable'];
        $this->GroupsMembers = TableRegistry::getTableLocator()->get('GroupsMembers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GroupsMembers);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

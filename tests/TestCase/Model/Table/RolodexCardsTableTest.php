<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RolodexCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RolodexCardsTable Test Case
 */
class RolodexCardsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\RolodexCardsTable
     */
    public $RolodexCards;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.members',
        'app.users',
        'app.groups'
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
            ->exists('RolodexCards') ? [] : ['className' => RolodexCardsTable::class];
        $this->RolodexCards = TableRegistry::getTableLocator()
            ->get('RolodexCards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RolodexCards);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
//        $this->RolodexCards->initialize();
        $this->assertTrue(is_a($this->RolodexCards->Identities, 'App\Model\Table\IdentitiesTable'),
            'Initialize() did not set up IdentitiesTable (alias for MembersTable).');
        pr('Memberships: ' . $this->RolodexCards->Memberships);
        $this->assertTrue(is_a($this->RolodexCards->Memberships, 'Cake\ORM\Association\BelongsTo'),
            'Initialize() did not set up MembershipsTable (alias for GroupsTable which '
            . 'creates the GroupIdentities layer).');
        pr('DataOwners: ' . $this->RolodexCards->DataOwners);
        $this->assertTrue(is_a($this->RolodexCards->DataOwners, 'Cake\ORM\Association\BelongsTo'),
            'Initialize() did not set up DataOwnersTable (alias for UsersTable).');
        
    }

    /**
     * Test findRolodexCards method
     *
     * @return void
     */
    public function testFindRolodexCards()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

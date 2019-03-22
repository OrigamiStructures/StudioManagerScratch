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
        
        $this->assertTrue(
            is_a(
                $this->RolodexCards->Identities, 
                'App\Model\Table\IdentitiesTable'),
            
            'Initialize() did not set up IdentitiesTable (alias for MembersTable).'
        );

        $this->assertTrue(
            is_a(
                $this->RolodexCards->associations()->get('Memberships'), 
                'Cake\ORM\Association\BelongsToMany'),
            
            'Initialize() did not set up MembershipsTable (alias for '
            . 'MembersTable which creates the Memberships layer).'
        );

        $this->assertTrue(
            is_a(
                $this->RolodexCards->associations()->get('DataOwners'), 
                'Cake\ORM\Association\BelongsTo'),
            
            'Initialize() did not set up DataOwnersTable (alias for UsersTable).'
        );
        
    }

    /**
     * Test findRolodexCards method
     *
     * @return void
     */
    public function testFindRolodexCardsBasicStructure()
    {
        $targets = ['layer' => 'identity', 'ids' => [2,3]];
        $cards = $this->RolodexCards->find('stackFrom', $targets);
        
        $this->assertTrue(
            is_a($cards, 'App\Model\Lib\StackSet'),
            'The found cards did not come packaged in a StackSet.'
        );
        
        $card = $cards->member(2);
        
        $this->assertTrue(
            is_a($card, 'App\Model\Entity\RolodexCard'),
            'The StackSet does not contain RolodexCard instances.'
        );
        pr($cards);
    }
}

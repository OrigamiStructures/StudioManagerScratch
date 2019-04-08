<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PersonCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PersonCardsTable Test Case
 */
class PersonCardsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PersonCardsTable
     */
    public $PersonCardsTable;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.person_cards',
        'app.data_owners',
        'app.memberships'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('PersonCards') ? [] : ['className' => PersonCardsTable::class];
        $this->PersonCardsTable = TableRegistry::getTableLocator()->get('PersonCards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PersonCardsTable);

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
     * Test initializeContactableCard method
     *
     * @return void
     */
    public function testInitializeContactableCard()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test loadFromAddress method
     *
     * @return void
     */
    public function testLoadFromAddress()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test marshalAddresses method
     *
     * @return void
     */
    public function testMarshalAddresses()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test loadFromContact method
     *
     * @return void
     */
    public function testLoadFromContact()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test marshalContacts method
     *
     * @return void
     */
    public function testMarshalContacts()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test initializeReceiverCard method
     *
     * @return void
     */
    public function testInitializeReceiverCard()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test loadFromDisposition method
     *
     * @return void
     */
    public function testLoadFromDisposition()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test marshalDispositions method
     *
     * @return void
     */
    public function testMarshalDispositions()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

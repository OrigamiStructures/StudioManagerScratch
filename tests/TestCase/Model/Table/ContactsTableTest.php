<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ContactsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ContactsTable Test Case
 */
class ContactsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ContactsTable
     */
    public $Contacts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.contacts'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Contacts') ? [] : ['className' => ContactsTable::class];
        $this->Contacts = TableRegistry::getTableLocator()->get('Contacts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Contacts);

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

    /**
     * Test beforeMarshal method
     *
     * @return void
     */
    public function testBeforeMarshal()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test spawn method
     *
     * @return void
     */
    public function testSpawn()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findContacts method
     *
     * @return void
     */
    public function testFindContacts()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findInMembers method
     *
     * @return void
     */
    public function testFindInMembers()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findKind method
     *
     * @return void
     */
    public function testFindKind()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findDetail method
     *
     * @return void
     */
    public function testFindDetail()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\PersonCard;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\PersonCard Test Case
 */
class PersonCardTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\PersonCard
     */
    public $PersonCard;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PersonCard = new PersonCard();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PersonCard);

        parent::tearDown();
    }

    /**
     * Test hasContacts method
     *
     * @return void
     */
    public function testHasContacts()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test contactEntities method
     *
     * @return void
     */
    public function testContactEntities()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test contactIDs method
     *
     * @return void
     */
    public function testContactIDs()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test contacts method
     *
     * @return void
     */
    public function testContacts()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test hasAddresses method
     *
     * @return void
     */
    public function testHasAddresses()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test addressEntities method
     *
     * @return void
     */
    public function testAddressEntities()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test addressIDs method
     *
     * @return void
     */
    public function testAddressIDs()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test addresses method
     *
     * @return void
     */
    public function testAddresses()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

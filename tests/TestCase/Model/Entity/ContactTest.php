<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Contact;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Contact Test Case
 */
class ContactTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\Contact
     */
    public $Contact;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Contact = new Contact();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Contact);

        parent::tearDown();
    }

    /**
     * Test getContact method
     *
     * @return void
     */
    public function testGetContact()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getLabel method
     *
     * @return void
     */
    public function testGetLabel()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test asString method
     *
     * @return void
     */
    public function testAsString()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test asArray method
     *
     * @return void
     */
    public function testAsArray()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isType method
     *
     * @return void
     */
    public function testIsType()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isPrimary method
     *
     * @return void
     */
    public function testIsPrimary()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

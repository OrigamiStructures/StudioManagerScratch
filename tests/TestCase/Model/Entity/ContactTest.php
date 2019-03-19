<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Contact;
use Cake\TestSuite\TestCase;
use App\Model\Table\Contacts;

/**
 * App\Model\Entity\Contact Test Case
 */
class ContactTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.contacts',
    ];
    /**
     * Test subject
     *
     * @var \App\Model\Entity\Contact
     */
    public $Contact;
    /**
     * Table model
     *
     * @var \App\Model\Table\Contacts
     */
    public $Contacts;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Contacts = $this->getTableLocator()->get('Contacts');
        $this->Contact = $this->Contacts->find('all')->toArray();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Contact, $this->Contacts);

        parent::tearDown();
    }

    /**
     * Test getContact method
     *
     * @return void
     */
    public function testGetContact()
    {
        $this->assertTrue(
				$this->Contact[0]
				->getContact() === 'ddrake@dreamingmind.com');
    }

    /**
     * Test getLabel method
     *
     * @return void
     */
    public function testGetLabel()
    {
        $this->assertTrue(
				$this->Contact[1]
				->getLabel() === 'phone');
    }

    /**
     * Test asString method with default delimeter
     *
     * @return void
     */
    public function testAsStringDefaultDelimeter()
    {
        $this->assertTrue(
				$this->Contact[0]
				->asString() === 'email: ddrake@dreamingmind.com');
    }

    /**
     * Test asString method with custom delimeter
     *
     * @return void
     */
    public function testAsStringCustomDelimeter()
    {
        $this->assertTrue(
				$this->Contact[3]
				->asString(': post to ') 
				=== 'curl-addr: post to dev.amp-fg.com/JSONStatusRequest');
    }

    /**
     * Test asArray method
     *
     * @return void
     */
    public function testAsArray()
    {
        $this->assertArraySubset(['email' => 'ddrake@dreamingmind.com'], $this->Contact[0]->asArray());
    }

    /**
     * Test isType method
     *
     * @return void
     */
    public function testIsType()
    {
        $this->assertTrue($this->Contact[3]->isType('curl-addr'),
				'Simple type check did not find a match');
    }

    /**
     * Test isType with a case-mismatched value
     *
     * @return void
     */
    public function testIsTypeWithCaseMismatch()
    {
        $this->assertTrue($this->Contact[3]->isType('Curl-Addr'),
				'Case-mismatch type check did not find a match');
    }

    /**
     * Test isType with no matching type
     *
     * @return void
     */
    public function testIsTypeWithNoMatch()
    {
        $this->assertFalse($this->Contact[3]->isType('no match'),
				'Search for non-existent type returned TRUE');
    }

    /**
     * Test isPrimary when true
     *
     * @return void
     */
    public function testIsPrimaryWhenTrue()
    {
        $this->assertTrue($this->Contact[0]->isPrimary(),
				'Did not detect when primary was set to 1');
    }
    /**
     * Test isPrimary when false
     *
     * @return void
     */
    public function testIsPrimaryWhenFalse()
    {
        $this->assertFalse($this->Contact[1]->isPrimary(),
				'Did not detect when primary was set to 0');
    }
    /**
     * Test isPrimary when null
     *
     * @return void
     */
    public function testIsPrimaryWhenNull()
    {
        $this->assertFalse($this->Contact[2]->isPrimary(),
				'Did not detect when primary was set to null');
    }
	
}

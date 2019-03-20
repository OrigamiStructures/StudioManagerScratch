<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Identity;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Identity Test Case
 */
class IdentityTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.members',
    ];
    /**
     * Test subject
     *
     * @var \App\Model\Entity\Member
     */
    public $Identity;
    public $Identities;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Identities = $this->getTableLocator()->get('Members');
        $this->Identity = $this->Identities->find('all')->toArray();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Identity, $this->Identities);

        parent::tearDown();
    }

/**
     * Test firstName method
     *
     * @return void
     */
    public function testFirstNamePassthrough()
    {
        $this->assertEquals('Don', $this->Identity[0]->firstName(),
            'A Passthrough method (firstName) into Members etity failed.');
    }

    /**
     * Test lastName method
     *
     * @return void
     */
    public function testLastNamePassthrough()
    {
        $this->assertEquals('Drake', $this->Identity[0]->lastName(),
            'A Passthrough method (lastName) into Members etity failed.');
    }

    /**
     * Test type method
     *
     * @return void
     */
    public function testTypePassthrough()
    {
        $this->assertEquals('Person', $this->Identity[0]->type(),
            'A Passthrough method (type) into Members etity failed.');
    }
}

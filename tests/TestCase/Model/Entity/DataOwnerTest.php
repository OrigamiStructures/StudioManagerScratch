<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\DataOwner;
use Cake\TestSuite\TestCase;
use App\Model\Table\DataOwnersTable;

/**
 * App\Model\Entity\DataOwner Test Case
 */
class DataOwnerTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\DataOwner
     */
    public $DataOwner;
	public $DataOwners;


	/**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->DataOwners = $this->getTableLocator()->get('DataOwners');
        $this->DataOwner = $this->DataOwners->find('hook')->toArray();
//		pr($this->DataOwner);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DataOwner, $this->DataOwners);

        parent::tearDown();
    }

    /**
     * Test id method for simple equivalence/non-equivalence
     *
     * @return void
     */
    public function testId()
    {
        $this->assertTrue($this->DataOwner[0]->id() === '008ab31c-124d-4e15-a4e1-45fccd7becac',
				'Failed to detect a matching id');
        $this->assertFalse($this->DataOwner[0]->id() === 'bad-124d-4e15-a4e1-45fccd7becac',
				'Incorrectly matched a non-matching id');
    }

    /**
     * Test userId method
     *
     * @return void
     */
    public function testUsername()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
    /**
     * Test userId method
     *
     * @return void
     */
    public function testOwnerOf()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

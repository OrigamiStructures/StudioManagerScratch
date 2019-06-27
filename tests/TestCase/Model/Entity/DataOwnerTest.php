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
//		debug($this->DataOwner);
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
     * Test username method
     *
     * @return void
     */
    public function testUsername()
    {
        $this->assertTrue($this->DataOwner[1]->username() === 'leonardo',
				'Failed to detect a matching username');
        $this->assertFalse($this->DataOwner[1]->username() === 'random data',
				'Incorrectly matched a non-matching username');
    }
	
    /**
     * Test ownerOf using a string
     *
     * @return void
     */
    public function testOwnerOfUsingString()
    {
        $this->assertTrue($this->DataOwner[0]->ownerOf('008ab31c-124d-4e15-a4e1-45fccd7becac'),
				'Failed to detect ownership-of based on a string');
        $this->assertFalse($this->DataOwner[0]->ownerOf('bad-124d-4e15-a4e1-45fccd7becac'),
				'Incorrectly detected ownership-of based on a string');
    }
	
    /**
     * Test ownerOf using an object
     *
     * @return void
     */
    public function testOwnerOfUsingObject()
    {
		$good_object = new \stdClass();
		$good_object->user_id = '008ab31c-124d-4e15-a4e1-45fccd7becac';
		$bad_object = new \stdClass();
		$bad_object->user_id = 'bad-124d-4e15-a4e1-45fccd7becac';
		
        $this->assertTrue($this->DataOwner[0]->ownerOf($good_object),
				'Failed to detect ownership-of based on an object property');
        $this->assertFalse($this->DataOwner[0]->ownerOf($bad_object),
				'Incorrectly detected ownership-of based on a non-matching object property');
    }
	
    /**
     * Test ownerOf using an object with missing property
     *
     * @return void
     */
    public function testOwnerOfUsingObjectMissingProperty()
    {
 		$bad_object = new \stdClass();
		$bad_object->other_property = 'bad-124d-4e15-a4e1-45fccd7becac';

		$this->assertFalse($this->DataOwner[0]->ownerOf($bad_object),
				'Failed to return false when checkin ownership on a missing property');
    }
	
    /**
     * Test ownerOf using an array
     *
     * @return void
     */
    public function testOwnerOfUsingArray()
    {
		$good_array = ['user_id' => '008ab31c-124d-4e15-a4e1-45fccd7becac'];
		$bad_array = ['user_id' => 'bad-124d-4e15-a4e1-45fccd7becac'];
		
        $this->assertTrue($this->DataOwner[0]->ownerOf($good_array),
				'Failed to detect ownership-of based on an array node');
        $this->assertFalse($this->DataOwner[0]->ownerOf($bad_array),
				'Incorrectly detected ownership-of based on a non-matching array node');
    }
	
    /**
     * Test ownerOf using an array
     *
     * @return void
     */
    public function testOwnerOfUsingArrayMissingNode()
    {
		$bad_array = ['other_node' => 'bad-124d-4e15-a4e1-45fccd7becac'];
		
        $this->assertFalse($this->DataOwner[0]->ownerOf($bad_array),
				'Failed to return false when checking ownership on a missing array node');
    }
	
	
	
}

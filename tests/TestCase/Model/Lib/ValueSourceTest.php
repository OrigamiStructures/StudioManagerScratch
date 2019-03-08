<?php
namespace App\Test\TestCase\Model\Lib;

use App\Model\Lib\ValueSource;
use Cake\TestSuite\TestCase;
use App\Model\Entity\Address;
use Cake\Utility\Inflector;

/**
 * App\Model\Lib\ValueSource Test Case
 */
class ValueSourceTest extends TestCase
{
	
	public $addressRecords;
	public $Addresses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.addresses',
    ];
	
	/**
     * Test subject
     *
     * @var \App\Model\Lib\ValueSource
     */
    public $ValueSource;

	public function setUp() {
		parent::setUp();
        $this->Addresses = $this->getTableLocator()->get('Addresses');
        
        $this->addressRecords = $this->Addresses->find('all')
            ->select(['id', 'address1', 'address2', 'address3',
                'city', 'state', 'zip', 'country'])
            ->where(['id IN' => [1,30,31,32,33,34,35,36,37,38,39]])
            ->toArray();
	Inflector::singularize('addresss');

//5664 Sunridge Court, Castro Valley CA 94552
//Address Line 1
//Address unknown
//Address unknown
//City
//State
//Zip
//Address unknown
//Address Line 1, City State
//Address Line 1, City Zip
//State Zip

	}
	
	public function tearDown() {
        unset($this->Address);
        unset($this->addressRecords);
		parent::tearDown();
	}
    /**
     * Test __construct method VALID
     *
	 * @dataProvider constructors
     * @return void
     */
    public function testValidConstruct($entity, $source, $message)
    {
        $this->ValueSource = new ValueSource($entity, $source);
		$this->assertTrue($this->ValueSource->isValid(), $message);
		unset($this->ValueSource);
    }
	
	public function constructors() {
		return [
			['address', 'address1', "Construct with a lowercase clase name "
				. "and a good field produces notValid state."],
			['Address', 'address1', "Construct with a upper clase name "
				. "and a good field produces notValid state."],
			['dispositionsPiece', 'piece_id', "Construct with a camel clase name "
				. "and a good field produces notValid state."],
			['address', 'bad', "Construct with a lowercase clase name "
				. "and a bad field produces Valid state."],
		];
	}

    /**
     * Test __construct method VALID
     *
	 * @dataProvider badConstructors
     * @return void
     */
    public function testInValidConstruct($entity, $source, $message)
    {
        $this->ValueSource = new ValueSource($entity, $source);
		$this->assertFalse($this->ValueSource->isValid(), $message);
		unset($this->ValueSource);
    }
	
	public function badConstructors() {
		return [
			['dispositionsPiece', '', "Construct with a camel clase name "
				. "and a empty field produces notValid state."],
		];
	}

    /**
     * Test isValid method
     *
     * @return void
     */
    public function testIsValid()
    {
        $this->assertTrue(true);
    }

    /**
     * Test value method
     *
     * @return void
     */
    public function testValue()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

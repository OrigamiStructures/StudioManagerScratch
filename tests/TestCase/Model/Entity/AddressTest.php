<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Address;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Address Test Case
 */
class AddressTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\Address
     */
    public $Address;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Addresses = $this->getTableLocator()->get('Addresses');
        $this->addressRecords = $this->Pieces->find('all')
				->where(['id IN' => [1,30,31,32,33,34,35,36,37,38,39]])
				->toArray();
		print_r($this->addressRecords);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Address);

        parent::tearDown();
    }

    /**
     * Test _getAddressLine method
	 * 
	 * @dataProvider elevenEntities
     *
     * @return void
     */
    public function testAddressAsLine()
    {
		
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test addressArray method
     *
     * @return void
     */
    public function testAddressAsArray()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test cityStateZip method
     *
     * @return void
     */
    public function testCityStateZip()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
	
	public function elevenEntities() {
		return $this->addressRecords;
	}
}

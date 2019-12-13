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
     * @var \App\Model\Entity\Address
     */
    public $Address;

    /**
     * A set with 11 set and missing data variations
     *
     * @var Entity
     */
    public $addressRecords;

    /**
     * An entity with no values on any properties
     *
     * @var Entity
     */
    public $noAddressInfoRecord;

    /**
     * A set with various values for ->primary
     *
     * @var Entity
     */
    public $primaryVariants;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Addresses = $this->getTableLocator()->get('Addresses');

        $this->addressRecords = $this->Addresses->find('all')
            ->select(['id', 'address1', 'address2', 'address3',
                'city', 'state', 'zip', 'country'])
            ->where(['id IN' => [1,30,31,32,33,34,35,36,37,38,39]])
            ->toArray();

        $record = $this->Addresses->find('all')
            ->select(['id', 'address1', 'address2', 'address3',
                'city', 'state', 'zip', 'country'])
            ->where(['id' => 29])
            ->toArray();
        $this->noAddressInfoRecord = $record[0];

        $this->primaryVariants = $this->Addresses->find('all')
            ->select(['id', 'primary_addr'])
            ->where(['id IN' => [1,2,3]])
            ->toArray();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Address);
        unset($this->addressRecords);

        parent::tearDown();
    }

     /**
     * Test cityStateZip with no properties set
     *
     * @return void
     */
    public function testIsPrimary() {
        $data = $this->primaryProviders();
        foreach($data as $datum) {
           $this->assertEquals($datum[0], $datum[1]->isPrimary());
        }
    }

    /**
     * Build a [result, entity] array for the tests
     *
     * @return array
     */
    public function primaryProviders() {
        $result = [
            FALSE,
            TRUE,
            FALSE,
        ];
        $provider = [];
        foreach ($this->threePrimaries() as $key => $entity) {
            $provider[] = [$result[$key], $entity];
        }
        return $provider;
    }

     /**
     * Test cityStateZip with no properties set
     *
     * @return void
     */
    public function testCityStateZipNoData() {
        $this->assertEquals('', $this->noAddressInfoRecord->CityStateZip());
    }

     /**
     * Test cityStateZip with no properties set
     *
     * @return void
     */
    public function testAsStringNoData() {
        $this->assertEquals('Address unknown', $this->noAddressInfoRecord->asString());
    }

     /**
     * Test cityStateZip with no properties set
     *
     * @return void
     */
    public function testAsArraya() {
        $this->assertEquals([], $this->noAddressInfoRecord->asArray());
    }

     /**
     * Test cityStateZip method
     *
     * @return void
     */
    public function testCityStateZip()
    {
        $data = $this->elevenZipProviders();
        foreach($data as $datum) {
           $this->assertEquals($datum[0], $datum[1]->cityStateZip());
        }
    }

   /**
     * Test _getAddressLine method
     *
     *
     * @return void
     */
    public function testAsString()
    {
        $data = $this->elevenLineProviders();
        foreach($data as $datum) {
           $this->assertEquals($datum[0], $datum[1]->asString());
        }
    }

    /**
     * Test addressArray method
     *
     * @return void
     */
    public function testAsArray()
    {
        $data = $this->elevenArrayProviders();
        foreach($data as $datum) {
           $this->assertEquals($datum[0], $datum[1]->asArray());
        }
    }

    /**
     * Build a [result, entity] array for the tests
     *
     * @return array
     */
    public function elevenLineProviders() {
        $result = [
            '5664 Sunridge Court, Castro Valley CA 94552',
            'Address Line 1',
            'Address unknown',
            'Address unknown',
            'City',
            'State',
            'Zip',
            'Address unknown',
            'Address Line 1, City State',
            'Address Line 1, City Zip',
            'State Zip',
        ];
        $provider = [];
        foreach ($this->elevenEntities() as $key => $entity) {
            $provider[] = [$result[$key], $entity];
        }
        return $provider;
    }

    /**
     * Build a [result, entity] array for the tests
     *
     * @return array
     */
    public function elevenZipProviders() {
        $result = [
            'Castro Valley CA 94552',
            '',
            '',
            '',
            'City',
            'State',
            'Zip',
            '',
            'City State',
            'City Zip',
            'State Zip',
        ];
        $provider = [];
        foreach ($this->elevenEntities() as $key => $entity) {
            $provider[] = [$result[$key], $entity];
        }
        return $provider;
    }


    /**
     * Build a [result, entity] array for the tests
     *
     * @return array
     */
    public function elevenArrayProviders() {
        $result = [
            [
                '5664 Sunridge Court',
                'In the back',
                'Castro Valley CA 94552',
                'USA',
            ],
            [
                'Address Line 1',
            ],
            [
                'Address Line 2',
            ],
            [
                'Address Line 3',
            ],
            [
                'City',
            ],
            [
                'State',
            ],
            [
                'Zip',
            ],
            [
                'Country',
            ],
            [
                'Address Line 1',
                'Address Line 2',
                'City State',
            ],
            [
                'Address Line 1',
                'Address Line 3',
                'City Zip',
                'Country',
            ],
            [
                'State Zip',
            ],
        ];
        $provider = [];
        foreach ($this->elevenEntities() as $key => $entity) {
            $provider[] = [$result[$key], $entity];
        }
        return $provider;
    }

    /**
     * Show what this properties data looks like
     *
     * @return array
     */
    public function threePrimaries() {
        return $this->primaryVariants;

//    [0] => App\Model\Entity\Address Object
//        (
//            [id] => 1
//            [primary_addr] =>
//        )
//    [1] => App\Model\Entity\Address Object
//        (
//            [id] => 2
//            [primary_addr] => 1
//        )
//    [2] => App\Model\Entity\Address Object
//        (
//            [id] => 3
//            [primary_addr] => 0
//        )
    }

    /**
     * Show what this properties data looks like
     *
     * @return array
     */
    public function elevenEntities() {

        return $this->addressRecords;

//    [0] => App\Model\Entity\Address Object
//        (
//            [id] => 1
//            [address1] => 5664 Sunridge Court
//            [address2] => In the back
//            [address3] =>
//            [city] => Castro Valley
//            [state] => CA
//            [zip] => 94552
//            [country] => USA
//            [label] => main
//        )
//
//    [1] => App\Model\Entity\Address Object
//        (
//            [id] => 30
//            [address1] => Address Line 1
//            [address2] =>
//            [address3] =>
//            [city] =>
//            [state] =>
//            [zip] =>
//            [country] =>
//            [label] => main
//        )
//
//    [2] => App\Model\Entity\Address Object
//        (
//            [id] => 31
//            [address1] =>
//            [address2] => Address Line 2
//            [address3] =>
//            [city] =>
//            [state] =>
//            [zip] =>
//            [country] =>
//            [label] => main
//        )
//
//    [3] => App\Model\Entity\Address Object
//        (
//            [id] => 32
//            [address1] =>
//            [address2] =>
//            [address3] => Address Line 3
//            [city] =>
//            [state] =>
//            [zip] =>
//            [country] =>
//            [label] => main
//        )
//
//    [4] => App\Model\Entity\Address Object
//        (
//            [id] => 33
//            [address1] =>
//            [address2] =>
//            [address3] =>
//            [city] => City
//            [state] =>
//            [zip] =>
//            [country] =>
//            [label] => main
//        )
//
//    [5] => App\Model\Entity\Address Object
//        (
//            [id] => 34
//            [address1] =>
//            [address2] =>
//            [address3] =>
//            [city] =>
//            [state] => State
//            [zip] =>
//            [country] =>
//            [label] => main
//        )
//
//    [6] => App\Model\Entity\Address Object
//        (
//            [id] => 35
//            [address1] =>
//            [address2] =>
//            [address3] =>
//            [city] =>
//            [state] =>
//            [zip] => Zip
//            [country] =>
//            [label] => main
//        )
//
//    [7] => App\Model\Entity\Address Object
//        (
//            [id] => 36
//            [address1] =>
//            [address2] =>
//            [address3] =>
//            [city] =>
//            [state] =>
//            [zip] =>
//            [country] => Country
//            [label] => main
//        )
//
//    [8] => App\Model\Entity\Address Object
//        (
//            [id] => 37
//            [address1] => Address Line 1
//            [address2] => Address Line 2
//            [address3] =>
//            [city] => City
//            [state] => State
//            [zip] =>
//            [country] =>
//            [label] => main
//        )
//
//    [9] => App\Model\Entity\Address Object
//        (
//            [id] => 38
//            [address1] => Address Line 1
//            [address2] =>
//            [address3] => Address Line 3
//            [city] => City
//            [state] =>
//            [zip] => Zip
//            [country] => Country
//            [label] => main
//        )
//
//    [10] => App\Model\Entity\Address Object
//        (
//            [id] => 39
//            [address1] =>
//            [address2] =>
//            [address3] =>
//            [city] =>
//            [state] => State
//            [zip] => Zip
//            [country] =>
//            [label] => main
//        )
    }
}

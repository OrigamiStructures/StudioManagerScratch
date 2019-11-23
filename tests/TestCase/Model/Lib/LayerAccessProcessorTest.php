<?php

namespace App\Test\TestCase\Model\Lib;

use App\Model\Entity\Member;
use App\Model\Lib\LayerAccessProcessor;
use Cake\TestSuite\TestCase;

/**
 * Class LayerAccessProcessorTest
 * @package App\Test\TestCase\Model\Lib
 */
class LayerAccessProcessorTest extends TestCase
{

    /**
     * @var LayerAccessProcessor
     */
    protected $lap;

    /**
     *
     */
    public function setUp()
    {
        $this->lap = new LayerAccessProcessor('member');
    }

    /**
     *
     */
    public function tearDown()
    {
        unset($this->lap);
    }

    //<editor-fold desc="Main behaviors">
    /**
     *
     */
    public function testGetAppendIterator()
    {
        $this->assertInstanceOf('App\Model\Lib\LayerAppendIterator', $this->lap->getAppendIterator(),
            'getAppendIterator() did not get it');
    }

    /**
     * Insert data into the pool for processing (ApppendIterator)
     */
    public function testInsert()
    {
        $aLayer = $this->getLayerData();
        $anArray = $this->getArrayData();
        $anEntity = $this->getEntityData();
        $ai = $this->lap->getAppendIterator();

        $this->lap->insert($anArray);
        $this->assertCount(4, $ai,
            'insert(anArray) of entitities did not yield the expected iterator count');

        $this->lap->insert($aLayer);
        $this->assertCount(6, $ai,
            'insert(aLayer) of entitities did not yield the expected iterator count');

        $this->lap->insert($anEntity);
        $this->assertCount(7, $ai,
            'insert(anEntity) did not yield the expected iterator count');
    }

    /**
     *
     */
    public function testPerform()
    {

    }
    //</editor-fold>

    //<editor-fold desc="LayerAccessInterface implementations">
    /**
     *
     */
    public function testToLayerDirectNoProcessing()
    {
        $anArray = $this->getArrayData();
        $this->lap->insert($anArray);
        $actual = $this->lap->toLayer();

        $this->assertInstanceOf('App\Model\Lib\Layer', $actual,
            'toLayer() directly on the LayerAccessProcessor did not produce a Layer');
        $this->assertCount(4, $actual,
            'toLayer() directly on the LayerAccessProcessor did not produce '
            . 'a result array of the proper size');
    }

    /**
     *
     */
    public function testToArrayDirectNoProcessing()
    {
        $aLayer = $this->getLayerData();
        $this->lap->insert($aLayer);
        $actual = $this->lap->toArray();

        $this->assertTrue(is_array($actual),
            'toArray() directly on the LayerAccessProcessor did not produce an array');
        $this->assertCount(2, $actual,
            'toArray() directly on the LayerAccessProcessor did not produce '
            . 'a result array of the proper size');
    }

    /**
     *
     */
    public function testToKeyValueList()
    {
        $aLayer = $this->getLayerData();
        $this->lap->insert($aLayer);

        $actual = $this->lap->toKeyValueList('id', 'name');
        $expected = [
            1 => 'one ONE',
            2 => 'two TWO'
        ];

        $this->assertTrue($expected == $actual,
            'toKeyValueList() didn\t produce the expected array');
    }

    /**
     *
     */
    public function testToValueList()
    {
        $aLayer = $this->getLayerData();
        $this->lap->insert($aLayer);

        $actual = $this->lap->toValueList('first_name');
        $expected = ['one', 'two'];

        $this->assertTrue($expected == $actual,
            'toValueList() didn\t produce the expected array');
    }

    /**
     *
     */
    public function testGetValueRegistry()
    {

    }

    /**
     *
     */
    public function testToDistinctList()
    {

    }
    //</editor-fold>

    //<editor-fold desc="Introspection">
    /**
     * The insert() test gives this a thorough workout.
     */
    public function testRawCount()
    {
        $ai = $this->lap->getAppendIterator();
        $this->assertCount(0, $ai);

    }

    /**
     *
     */
    public function testResultCount()
    {
        $this->assertTrue(0 === $this->lap->resultCount(),
            'ResutlIterator is not set to an empty array after construction');

        $aLayer = $this->getLayerData();
        $this->lap->insert($aLayer);
        $actual = $this->lap->toArray();

        $this->assertEquals(2, $this->lap->resultCount(),
            'ResultIterator does not contain the expected number of '
            . 'items after return data');

    }
    //</editor-fold>

    //<editor-fold desc="ArgObj tests">
    /**
     *
     */
    public function testFind()
    {
        $actual = $this->lap->find();
        $this->assertInstanceOf('App\Model\Lib\LayerAccessArgs', $actual,
            'find() did not return a LayerAccessArgs instance');
    }

    /**
     *
     */
    public function testSetArgObj()
    {

    }

    /**
     *
     */
    public function testClearAccessArgs()
    {

    }

    /**
     *
     */
    public function testCloneArgObj()
    {

    }
    //</editor-fold>

    //<editor-fold desc="Simple Fixture makers">
    /**
     * Get 2 Member entities in a Layer
     *
     * @return \App\Model\Lib\Layer
     */
    protected function getLayerData()
    {
        $seeds = [1 =>'one', 'two'];
        $data = [];
        foreach ($seeds as $id => $seed) {
            $data[] = $this->getEntityData($id, $seed);
        }
        return layer($data);
    }

    /**
     * Get 4 Member entities in an array
     *
     * @return array
     */
    protected function getArrayData()
    {
        $seeds = [3 => 'three', 'four', 'five', 'six'];
        $data = [];
        foreach ($seeds as $id => $seed) {
            $data[] = $this->getEntityData($id, $seed);
        }
        return $data;
    }

    /**
     * Get one Member entity
     *
     * @param int $id
     * @param string $seed
     * @return Member
     */
    protected function getEntityData($id = 0, $seed = 'zero')
    {
        $entityData = [
            'id' => $id,
            'first_name' => $seed,
            'last_name' => strtoupper($seed),
            'member_type' => 'Person'
        ];
        return new Member($entityData);
    }
    //</editor-fold>

}

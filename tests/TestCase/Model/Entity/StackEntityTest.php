<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Table\ArtStacksTable;
use Cake\ORM\TableRegistry;
use App\Model\Entity\StackEntity;
use Cake\TestSuite\TestCase;
use App\Lib\Layer;
use App\ORM\Entity\Address;

/**
 * App\Model\Entity\StackEntity Test Case
 */
class StackEntityTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\StackEntity
     */
    public $StackEntity;

    /**
     * Test subject
     *
     * @var \App\Model\Table\ArtStacksTable
     */
    public $ArtStacks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
//        'app.art_stacks',
        'app.artworks',
        'app.editions',
        'app.formats',
        'app.pieces',
        'app.dispositions_pieces'
    ];
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ArtStacks') ? [] : ['className' => ArtStacksTable::class];
        $this->ArtStacks = TableRegistry::getTableLocator()->get('ArtStacks', $config);
        $artID = 4; //jabberwocky
        $stacks = $this->ArtStacks->find('stackFrom', ['layer' => 'artworks', 'ids' => [$artID]]);
        $this->StackEntity = $stacks->owner('artwork', $artID, 'first');
        
        $this->StackEntity->arrayProp = ['a','b','c'];
        $this->StackEntity->stringProp = 'This is a string property';
        $this->StackEntity->numProp = 498;
                
        //art 4, ed 5 Unique qty 1, ed 8 Open Edition qty 150
        //fmt 5 desc Watercolor 6 x 15", fmt 8 desc Digital output with cloth-covered card stock covers
        //pc 20 nm null qty 1, pc 38,40,509,955 qty 140,7,1,2
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->StackEntity);
        unset($this->ArtStacks);

        parent::tearDown();
    }
    
    

// <editor-fold defaultstate="collapsed" desc="Load method tests">

    /**
     * Test load method
     * 
     * These all pass through to Layer and that class tests edge cases.
     * So all I need here is verification of the proper passthrough 
     * of all the variants
     *
     * @return void
     */
    public function testLoad() {
        $format = $this->StackEntity->load('formats', 5);
        $this->assertEquals('Watercolor 6 x 15"', $format->description);

        $format = $this->StackEntity->load('formats', [8]);
        $this->assertStringStartsWith('Digital output', $format->description);

        $pieces = $this->StackEntity->load('pieces', ['quantity', 140]);
        $piece = array_shift($pieces);
        $this->assertEquals(140, $piece->quantity);

        $this->assertEquals(5, count($this->StackEntity->load('pieces', 'all')));

        $this->assertEquals(2, count($this->StackEntity->load('formats', ['all'])));

        $this->assertEquals(1, count($this->StackEntity->load('pieces', 'first')));

        $this->assertEquals(1, count($this->StackEntity->load('formats', ['first'])));

        // unknown layer combinded with a field search
        $this->assertEquals(0, count($this->StackEntity->load('first', ['edition_id', 8])));
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Simple class methods">

    /**
     * Test exists method
     * 
     * @return void
     */
    public function testExists()     {
        $this->assertFalse($this->StackEntity->exists('artwork', 50));
        $this->assertFalse($this->StackEntity->exists('something', 6));
        $this->assertTrue($this->StackEntity->exists('artwork', 4));
        $this->assertTrue($this->StackEntity->exists('editions', 8));
        $this->assertTrue($this->StackEntity->exists('formats', 5));
        $this->assertTrue($this->StackEntity->exists('pieces', 955));
    }

    /**
     * Test count method
     *
     * @return void
     */
    public function testCount()     {
        $this->assertEquals(1, $this->StackEntity->count('artwork'));
        $this->assertEquals(5, $this->StackEntity->count('pieces'));
        $this->assertEquals(2, $this->StackEntity->count('formats'));
    }

    /**
     * Test hasNo method
     *
     * @return void
     */
    public function testHasNo()     {
        $this->assertFalse($this->StackEntity->hasNo('editions'), 'has no editions');
        $this->assertTrue($this->StackEntity->hasNo('members'), 'has no members');
    }

    /**
     * Test primaryLayer method
     *
     * @return void
     */
    public function testPrimaryLayer()     {
        $this->assertEquals('artwork', $this->StackEntity->primaryLayer());
    }

    /**
     * Test primaryId method
     *
     * @return void
     */
    public function testPrimaryId()     {
        $this->assertEquals(4, $this->StackEntity->primaryId());
    }

    /**
     * Test primaryEntity method
     *
     * @return void
     */
    public function testPrimaryEntity()     {
        $entity = $this->StackEntity->primaryEntity();
        $this->assertInstanceOf('\App\Model\Entity\Artwork', $entity);
    }

    /**
     * Test distinct method
     *
     * @return void
     */
    public function testDistinct()     {
        $this->assertEquals([5, 8], $this->StackEntity->distinct('pieces', 'edition_id'));
        $this->assertEquals([5, 8], $this->StackEntity->distinct('formats', 'edition_id'));
    }

    /**
     * Test IDs method
     *
     * @return void
     */
    public function testIDs()     {
        $this->assertEquals([20, 38, 40, 509, 955], $this->StackEntity->IDs('pieces'));
        $this->assertEquals([4], $this->StackEntity->IDs('artwork'));
    }

    /**
     * Test linkedTo method
     *
     * @return void
     */
    public function testLinkedTo()     {
        $unique = $this->StackEntity->linkedTo('pieces', ['edition_id', 5]);
        $open = $this->StackEntity->linkedTo('pieces', ['edition_id', 8]);
        $none = $this->StackEntity->linkedTo('something', ['link', 12]);


        $this->assertEquals(1, count($unique), 'unique edition');
        $this->assertEquals(4, count($open), 'open edition');
        $this->assertEquals(0, count($none), 'nothing');
    
    }

// </editor-fold>
    
// <editor-fold defaultstate="collapsed" desc="Modified parent methods">
    
    /**
     * Test extension of isEmpty method
     *
     * @return void
     */
    public function testIsEmpty(){
        $this->assertTrue($this->StackEntity->isEmpty('member'), 'An uset property');
        $emptyLayer = new Layer([], 'addresses');
        $this->StackEntity->addresses = $emptyLayer;
        $this->assertTrue($this->StackEntity->isEmpty('addresses'), 'An empty layer property, count = 0');
    }
    
// </editor-fold>
    
// <editor-fold defaultstate="collapsed" desc="Inherited from entity">

    /**
     * Test set method extension
     *
     * @return void
     */
    public function testSet(){
        //extract an array, unset that value then 'set' the value
        //the set is a (string, value) arg arrangement
        $pieces = $this->StackEntity->load('pieces', 'all');
        $this->assertTrue(is_array($pieces), 'the load value is an array');
        
        unset($this->StackEntity->pieces);
        $this->assertTrue($this->StackEntity->isEmpty('pieces'), 'piece value is gone');
        
        $this->StackEntity->set('pieces', $pieces);
        $this->assertInstanceOf('\App\Lib\Layer', $this->StackEntity->get('pieces'), 'layer object was made');
        
        //do the same process to multiple values
        //to test the [prop=>val, prop=>val] arguement syntax
        $pieces = $this->StackEntity->load('pieces', 'all');
        $dp = $this->StackEntity->load('dispositionsPieces', 'all');
        unset($this->StackEntity->pieces);
        unset($this->StackEntity->dispositionsPieces);
        
        $this->assertTrue($this->StackEntity->isEmpty('pieces'), 'piece value is gone');
        $this->assertTrue($this->StackEntity->isEmpty('dispositionsPieces'), 'piece value is gone');
        
        $this->StackEntity->set(['pieces' => $pieces, 'dispositionsPieces' => $dp, 'something' => ['array']]);
        
        $this->assertInstanceOf('\App\Lib\Layer', $this->StackEntity->get('pieces'), 'layer object was made');
        $this->assertInstanceOf('\App\Lib\Layer', $this->StackEntity->get('dispositionsPieces'), 'layer object was made');
        $this->assertTrue(is_array($this->StackEntity->get('something')), 'array was set');
        
        //do the same process to multiple values and use the guard feature
        //to test the [prop=>val, prop=>val] arguement syntax
//        $pieces = $this->StackEntity->load('pieces', 'all');
//        $dp = $this->StackEntity->load('dispositionsPieces', 'all');
//        unset($this->StackEntity->pieces);
//        unset($this->StackEntity->dispositionsPieces);
//        
//        $this->assertTrue($this->StackEntity->isEmpty('pieces'), 'piece value is gone');
//        $this->assertTrue($this->StackEntity->isEmpty('dispositionsPieces'), 'piece value is gone');
//        
//        $this->StackEntity->set(['pieces' => $pieces, 'dispositionsPieces' => $dp, 'something' => ['array']]);
//        
//        $this->assertInstanceOf('\App\Lib\Layer', $this->StackEntity->get('pieces'), 'layer object was made');
//        $this->assertInstanceOf('\App\Lib\Layer', $this->StackEntity->get('dispositionsPieces'), 'layer object was made');
//        $this->assertTrue(is_array($this->StackEntity->get('something')), 'array was set');
    }
    
    /**
     * Test has method
     *
     * @return void
     */
    public function testSetx(){
        
    }
    /**
     * Test has method
     *
     * @return void
     */
    public function testGet(){
        $this->assertInstanceOf('\App\Lib\Layer', $this->StackEntity->get('pieces'));
        $this->assertEquals(null, $this->StackEntity->get('members'));
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testGetOriginal(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testGetOriginalValues(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testHas() {
        $this->assertTrue($this->StackEntity->has('editions'), 'has editions');
        $this->assertFalse($this->StackEntity->has('members'), 'has members');
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testHasValue(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testUnsetProperty(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testHiddenProperties(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testSetHidden(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testGetHidden(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testProperties(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testSetVirtual(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testGetVirtual(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testVisibleProperties(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testToArray(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testJsonSerialize(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testOffsetExists(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testOffsetGet(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testOffsetSet(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testOffsetUnset(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testExtract(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testExtractOriginal(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testExtractOriginalChanged(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testDirty(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testSetDirty(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testIsDirty(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testGetDirty(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testClean(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testIsNew(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testGetErrors(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testGetError(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testSetErrors(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testSetError(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testErrors(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testGetInvalid(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testGetInvalidField(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testSetInvalid(){
        
    }
    
    /**
     * Test has method
     *
     * @return void
     */
    public function testSetInvalidField(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testInvalid(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testAccessible(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testSetAccess(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testIsAccessible(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testGetSource(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testSetSource(){
        
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testSource(){
        
    }

    

// </editor-fold>

}

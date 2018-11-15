<?php
namespace App\Test\TestCase\Lib;

use App\Lib\Layer;
use Cake\TestSuite\TestCase;
use Cake\ORM\Locator\TableLocator;
use App\Exception\BadClassConfigurationException;

/**
 * App\Form\LayerForm Test Case
 */
class LayerTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.art_stack_pieces',
        'app.art_stack_artworks',
    ];

    /**
     * Test subject
     *
     * @var \App\Form\LayerForm
     */
    public $Layer;
    
    public $Pieces;
    public $Artworks;
    public $pieceRecords;
    public $artRecord;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Pieces = $this->getTableLocator()->get('Pieces');
        $this->pieceRecords = $this->Pieces->find('all')->toArray();
        $this->Artworks = $this->getTableLocator()->get('Artworks');
        $this->artRecord = $this->Artworks->find('all')->toArray();
        
        $chunks = array_chunk($this->pieceRecords, 5);
        $this->fivePieces = $chunks[1];

    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Pieces);
        unset($this->pieceRecords);
        unset($this->Artworks);
        unset($this->artRecord);
        unset($this->fivePieces);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testValidInitialization()
    {
        $record = $this->pieceRecords[0];
        
        $layer = new Layer([], 'edition');
        $this->assertInstanceOf('App\Lib\Layer', $layer);
        
        $layer = new Layer([$record], 'edition');
        $this->assertInstanceOf('App\Lib\Layer', $layer);
        
        $layer = new Layer([$record]);
        $this->assertInstanceOf('App\Lib\Layer', $layer);
    }
    
    /**
     * One of the two args must give a way to identify the layer type
     * 
     * @expectedException App\Exception\BadClassConfigurationException
     */
    public function testNoArgInitialization() {
        $piece = $this->pieceRecords[0];
        $art = $this->artRecord[0];
        $notEntityObj = new \stdClass();
        
        $this->expectExceptionMessageRegExp('/the name of the expected entity/');
        $layer = new Layer([], null);
    }
    
    /**
     * Can only accept Entity classes
     * 
     * @expectedException App\Exception\BadClassConfigurationException
     */
    public function testNotEntityInitialization() {
        $notEntityObj = new \stdClass();
        
        $this->expectExceptionMessageRegExp('/only accept objects that extend Entity/');
        $layer = new Layer([$notEntityObj], null);
    }
    
    /**
     * All entities must be the same class
     * 
     * @expectedException App\Exception\BadClassConfigurationException
     */
    public function testMixedEntityInitialization() {
        $piece = $this->pieceRecords[0];
        $art = $this->artRecord[0];

        $this->expectExceptionMessageRegExp('/must be of the same class/');
        $layer = new Layer([$piece, $art], null);
    }
        
    /**
     * entities must have an ->id property
     * 
     * @expectedException App\Exception\BadClassConfigurationException
     */
    public function testMissingIdInitialization() {
        $art = $this->artRecord[0];
        unset($art->id);
        
        $this->expectExceptionMessageRegExp('/expects to find \$entity->id/');
        $layer = new Layer([$art], null);
    }
        
    /**
     * test name property
     */
    public function testName() {
        $record = $this->pieceRecords[0];
        
        $layer = new Layer([], 'edition');
        $this->assertEquals('edition', $layer->name());
        
        $layer = new Layer([$record], 'edition');
        $this->assertEquals('piece', $layer->name());
        
        $layer = new Layer([$record]);
        $this->assertEquals('piece', $layer->name());
    }
    
    /**
     * test entityClass name property
     */
    public function testEntityClass() {
        $record = $this->pieceRecords[0];
        
        $layer = new Layer([], 'edition');
        $this->assertEquals('Edition', $layer->entityClass());
        
        $layer = new Layer([$record], 'edition');
        $this->assertEquals('Piece', $layer->entityClass());
        
        $layer = new Layer([$record]);
        $this->assertEquals('Piece', $layer->entityClass());
    }
 
    /**
     * test count method
     */
    public function testCount() {
        $layer = new Layer([], 'edition');
        $this->assertEquals(0, $layer->count());
        
        $layer = new Layer($this->pieceRecords);
        $this->assertEquals(53, $layer->count());
        
        $layer = new Layer($this->artRecord);
        $this->assertEquals(1, $layer->count());
    }
    
    public function testIDs() {
        $layer = new Layer($this->fivePieces);
        
        foreach ($this->fivePieces as $entity) {
            $this->assertContains($entity->id, $layer->IDs());
        }
        
    }
    
    public function testHas() {
        $layer = new Layer($this->fivePieces);

        $this->assertTrue($layer->has(965));
        $this->assertTrue($layer->has('962'));
        $this->assertFalse($layer->has(3));
        $this->assertFalse($layer->has('something wrong'));
    }
    
    public function testGetUsingId() {
        $layer = new Layer($this->fivePieces);

        $this->assertInstanceOf('App\Model\Entity\Piece', $layer->get(965));
        $this->assertInstanceOf('App\Model\Entity\Piece', $layer->get('962'));
        $this->assertNull($layer->get(3));
        $this->assertNull($layer->get('something wrong'));
    }
    
    public function testGetUsingPropertyValue() {
        $layer = new Layer($this->fivePieces);
        
        $results = $layer->get('number', 4); // good find
        $this->assertTrue(is_array($results));
        $match = array_pop($results);
        $this->assertEquals(4, $match->number);
        
        $results = $layer->get('number', '4'); // good val, casting mismatch
        $this->assertTrue(is_array($results));
        $match = array_pop($results);
        $this->assertEquals(4, $match->number);
        
        $results = $layer->get('number', 9000); // val doesn't exist
        $this->assertTrue(is_array($results));
        $this->assertTrue(empty($results));

        $results = $layer->get('boogers', 3); // property doesn't exist
        $this->assertTrue(is_array($results));
        $this->assertTrue(empty($results));
    }
    
    public function testGetUsingAll() {
        $layer = new Layer($this->fivePieces);
        
        $this->assertEquals(5, count($layer->get('all')));
        $this->assertEquals(5, count($layer->get('all', ['id', 12])));        
    }
    
    public function testGetUsingFirst() {
        $layer = new Layer($this->fivePieces);
        
        $this->assertEquals(1, count($layer->get('first')));
        $this->assertEquals(1, count($layer->get('first', ['disposition_count', 0])));        
        $this->assertEquals(0, count($layer->get('first', ['boogers', 0])));        
        $this->assertEquals(0, count($layer->get('first', ['disposition_count', 50])));        
    }
    
    /**
     * Test filter
     * 
     * Same testing pattern as testGetUsingPropertyValue()
     */
    public function testFilter() {
        $layer = new Layer($this->fivePieces);
        
        $results = $layer->filter('number', 4); // good find
        $this->assertTrue(is_array($results));
        $match = array_pop($results);
        $this->assertEquals(4, $match->number);
        
        $results = $layer->filter('number', '4'); // good val, casting mismatch
        $this->assertTrue(is_array($results));
        $match = array_pop($results);
        $this->assertEquals(4, $match->number);
        
        $results = $layer->filter('number', 9000); // val doesn't exist
        $this->assertTrue(is_array($results));
        $this->assertTrue(empty($results));

        $results = $layer->filter('boogers', 3); // property doesn't exist
        $this->assertTrue(is_array($results));
        $this->assertTrue(empty($results));
    }

    /**
     * Check that no entities have changed
     */
    public function testIsClean() {
        $layer = new Layer($this->fivePieces);
        $this->assertTrue($layer->isClean());
        
        $piece = new \App\Model\Entity\Piece(['id' => 400000]);
        $layer = new Layer([$piece]);
        $this->assertFalse($layer->isClean());
    }
    
    /**
     * Test access to belongsTo sets
     */
    public function testLinkedTo() {
        $layer = new Layer($this->pieceRecords);
        $this->assertEquals(5, count($layer->linkedTo('format', 36)));
        $this->assertEquals(40, count($layer->linkedTo('format', null)));
        $this->assertEquals(0, count($layer->linkedTo('format', 500)));
        $this->assertEquals(0, count($layer->linkedTo('junk', 36)));
    }
    
    /**
     * Test sorting
     * 
     * @todo Test character type sorting too
     */
    public function testSort() {
        $layer = new Layer($this->fivePieces);
        
        $result = $layer->sort('number'); //DESC default
        $first = $result[0]->number;
        $middle = $result[2]->number;
        $last = $result[4]->number;
        $this->assertTrue(($first > $middle) && ($middle > $last));
        
        $result = $layer->sort('number', SORT_ASC);
        $first = $result[0]->number;
        $middle = $result[2]->number;
        $last = $result[4]->number;
        $this->assertTrue($first < $middle && $middle < $last);
    }
    
    
}
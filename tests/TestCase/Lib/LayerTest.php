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
//        $piece = $this->pieceRecords[0];
//        $art = $this->artRecord[0];
//        $notEntityObj = new \stdClass();
        
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
    public function testLayerName() {
        $record = $this->pieceRecords[0];
        
        $layer = new Layer([], 'edition');
        $this->assertEquals('edition', $layer->layerName());
        
        $layer = new Layer([$record], 'edition');
        $this->assertEquals('piece', $layer->layerName());
        
        $layer = new Layer([$record]);
        $this->assertEquals('piece', $layer->layerName());
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
            $this->assertContains($entity->id, $layer->IDs(),
					'IDs() on a layer of five elements did not return 5 IDs. '
					. 'At one expected id was missing');
        }
		
		$layer = new Layer([], 'contact');
		$this->assertEmpty($layer->IDs(), 'IDs() on an empty layer'
				. 'did not return an empty array.');
        
    }
    
    public function testHas() {
        $layer = new Layer($this->fivePieces);

        $this->assertTrue($layer->hasId(965));
        $this->assertTrue($layer->hasId('962'));
        $this->assertFalse($layer->hasId(3));
        $this->assertFalse($layer->hasId('something wrong'));
    }
	
	public function testLoadBare() {
		$layer = new Layer($this->fivePieces);
		$this->assertCount(5, $layer->load());
		$this->assertArrayHasKey(961, $layer->load());
	}
    
    public function testLoadUsingId() {
        $layer = new Layer($this->fivePieces);

		$id_int_965_arg = $layer->accessArgs()
				->lookupIndex(965);
        $this->assertInstanceOf('App\Model\Entity\Piece', $layer->load($id_int_965_arg));
		$id_string_965_arg = $layer->accessArgs()
				->lookupIndex('965');
		$this->assertInstanceOf('App\Model\Entity\Piece', $layer->load($id_string_965_arg));
		$id_3_bad_arg = $layer->accessArgs()
				->lookupIndex(3);
        $this->assertTrue(is_array($layer->load($id_3_bad_arg)));
 		$bad_index_arg = $layer->accessArgs()
				->lookupIndex('something wrong');
        $this->assertTrue(is_array($layer->load($bad_index_arg)));
    }
    
    public function testloadUsingPropertyValue() {
        $layer = new Layer($this->fivePieces);
        
 		$number_is_4_arg = $layer->accessArgs()
				->property('number')
				->filterValue(4);
        $results = $layer->load($number_is_4_arg); // good find
        $this->assertTrue(is_array($results));
        $match = array_pop($results);
        $this->assertEquals(4, $match->number);
        
 		$number_is_4_arg = $layer->accessArgs()
				->property('number')
				->filterValue('4');
        $results = $layer->load($number_is_4_arg); // good val, casting mismatch
        $this->assertTrue(is_array($results));
        $match = array_pop($results);
        $this->assertEquals(4, $match->number);
        
 		$number_is_badval_arg = $layer->accessArgs()
				->property('number')
				->filterValue(9000);
        $results = $layer->load($number_is_badval_arg); // val doesn't exist
        $this->assertTrue(is_array($results));
        $this->assertTrue(empty($results));

 		$badproperty_is_3_arg = $layer->accessArgs()
				->property('boogers')
				->filterValue(3);
        $results = $layer->load($badproperty_is_3_arg); // property doesn't exist
        $this->assertTrue(is_array($results));
        $this->assertTrue(empty($results));
    }
	
	public function testloadUsingPropertyArray() {
        $layer = new Layer($this->pieceRecords);
        
 		$number_is_4_arg = $layer->accessArgs()
				->property('number')
				->filterValue(4);
        $four = $layer->load($number_is_4_arg);
 		$number_is_3_arg = $layer->accessArgs()
				->property('number')
				->filterValue(3);
        $three = $layer->load($number_is_3_arg);
 		$number_is_3and4_arg = $layer->accessArgs()
				->property('number')
				->filterValue([4,3]);
        $results = $layer->load($number_is_3and4_arg); // good find
        $this->assertTrue((count($four) + count($three)) === count($results));
	}
    
    public function testLoadUsingAll() {
        $layer = new Layer($this->fivePieces);
        
 		$simpleAllArg = $layer->accessArgs()
				->limit('all');
        $this->assertEquals(5, count($layer->load($simpleAllArg)));
 		$all_id_equals_12 = $layer->accessArgs()
				->limit('all')
				->property('id')
				->filterValue('12');
        $this->assertEquals(0, count($layer->load($all_id_equals_12)));        
    }
    
    public function testloadUsingFirst() {
        $layer = new Layer($this->fivePieces);
        
 		$simpleFirstArg = $layer->
				accessArgs()
				->limit('first');
        $this->assertEquals(1, count($layer->load($simpleFirstArg)));
		
 		$first_with_0_dispos_arg = $layer->accessArgs()
				->limit('first')
				->property('disposition_count')
				->filterValue(0);
        $this->assertEquals(1, count($layer->load($first_with_0_dispos_arg)));  
		
 		$first_badSearch_args = $layer->accessArgs()
				->limit('first')
				->property('boogers')
				->filterValue(0);
        $this->assertEquals(0, count($layer->load($first_badSearch_args)));        
		
 		$first_with_50_dispos_arg = $layer->accessArgs()
				->limit(1)
				->property('disposition_count')
				->filterValue(50);
        $this->assertEquals(0, count($layer->load($first_with_50_dispos_arg)));        
    }
    
	/**
	 * Test element (a trait method) in Layer context
	 */
	public function testElement() {
		$layer = new Layer($this->fivePieces);
		$this->assertTrue($layer->element(0)->id === 961);
		$this->assertTrue($layer->element(6) === null);
	}
	
    /**
     * Test filter with property comparisons
     * 
     */
    public function testFilterWithProperties() {
        $layer = new Layer($this->fivePieces);
        
        $results = $layer->filter('number', 4); // good find
        $this->assertTrue(is_array($results), 
				'A valid search for \'number\' = 4 failed');
        $match = array_pop($results);
        $this->assertEquals(4, $match->number, 
				'A valid search for \'number\' = 4 failed to return an entity that had 4 on that property');
        
        $results = $layer->filter('number', '4'); // good val, casting mismatch
        $this->assertTrue(is_array($results), 
				'A valid search for \'number\' = \'4\' failed');
        $match = array_pop($results);
        $this->assertEquals(4, $match->number, 
				'A valid search for \'number\' = 4 failed to return an entity that had 4 on that property');
        
        $results = $layer->filter('number', 9000); // val doesn't exist
        $this->assertTrue(is_array($results));
        $this->assertTrue(empty($results));

        $results = $layer->filter('boogers', 3); // property doesn't exist
        $this->assertTrue(is_array($results));
        $this->assertTrue(empty($results));
    }

	/**
	 * Test filter with method comparisons
	 * 
	 */
	public function testFilterWithMethods() {
		$layer = new Layer($this->fivePieces);
		
		$result = $layer->filter('isCollected', '', 'truthy');
		$this->assertCount(2, $result, 'unexpected result while testing truthy '
				. 'on the boolean output of an entity method');
				
		$result = $layer->filter('isCollected', TRUE, '!=');
		$this->assertCount(3, $result, 'unexpected result while searching != '
				. 'on the boolean output of an entity method');
		
		$result = $layer->filter('key', '35_36', '==');
		$this->assertCount(5, $result, 'unexpected result while searching == '
				. 'on the string output of an entity method');
		
		$result = $layer->filter('key', '35_36', '!=');
		$this->assertCount(0, $result, 'unexpected result while searching != '
				. 'on the string output of an entity method');
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
    
    public function testDistinct() {
        $layer = new Layer($this->fivePieces);
        
        $distinct = $layer->distinct('number');
        sort($distinct);
        $this->assertEquals([1,2,3,4,5], $distinct);
        $this->assertEquals([36], $layer->distinct('format_id'));
        
//        foreach ($this->fivePieces as $entity) {
//            $this->assertContains($entity->id, $layer->IDs());
//        }
//        $this->markTestIncomplete('Not implemented yet.');
    }
    
}

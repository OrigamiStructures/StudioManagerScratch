<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Table\ArtStacksTable;
use Cake\ORM\TableRegistry;
use App\Model\Entity\StackEntity;
use Cake\TestSuite\TestCase;
use App\Model\Lib\Layer;
use App\ORM\Entity\Address;
use App\Exception\BadClassConfigurationException;

/**
 * App\Model\Entity\StackEntity Test Case
 */
class StackEntityTest extends TestCase
{

    /**
     * The StackEntity for testing
     *
     * @var \App\Model\Entity\StackEntity
     */
    public $StackEntity;

    /**
     * The table object
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
        $this->ArtStacks = TableRegistry::getTableLocator()->get('ArtStacks', []);
        $artID = 4; //jabberwocky
        $stacks = $this->ArtStacks
				->find('stacksFor', ['seed' => 'artworks', 'ids' => [$artID]]);
        $this->StackEntity = $stacks->ownerOf('artwork', $artID, 'first');
        
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
		\Cake\Cache\Cache::clear(FALSE, $this->ArtStacks->cacheName());
        unset($this->StackEntity);
        unset($this->ArtStacks);

        parent::tearDown();
    }
    

	/**
	 * Test find method
	 *
	 * @return void
	 */
	public function testFind() {
        $arg = $this->StackEntity->find();
        $this->assertTrue(is_a($arg, 'App\Model\Lib\LayerAccessArgs'),
            'find() did not create a LayerAccessArgs object');
        $this->assertTrue(is_a($arg->data(), 'App\Model\Entity\StackEntity'),
            'The access object created by find() did not contain the expected data');
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
		
		$this->assertCount(1, $this->StackEntity->load());
		$this->assertArrayHasKey(4, $this->StackEntity->load());
		
		$formats_arg = $this->StackEntity->accessArgs()->setLayer('formats');
		
		$this->assertCount(2, $this->StackEntity->load($formats_arg));
		$format_index_5 = $this->StackEntity
				->accessArgs()
				->setLayer('formats')
				->setIdIndex(1);
        $format = $this->StackEntity->load($format_index_5);
        $this->assertEquals('Watercolor 6 x 15"', $format->description,
				'loading a valid format by exposed id ...->load(\'formats\', 5)... '
				. 'did not return an entity with an expected property value.');
		
		$pieces_qty_equals_140 = $this->StackEntity->accessArgs()
				->setLayer('pieces')
				->setAccessNodeObject('value', 'quantity')
				->filterValue(140);
        $pieces = $this->StackEntity->load($pieces_qty_equals_140);
        $piece = array_shift($pieces);
        $this->assertEquals(
				140, 
				$piece->quantity,
				'loading a valid format by property/value test ...'
				. '->load(\'pieces\', [\'quantity\', 140])... did not '
				. 'return an entity with an expected property value.');

		
		$all_pieces_args = $this->StackEntity->accessArgs()
				->setLayer('pieces')
				->setLimit('all');
        $this->assertEquals(
				5, 
				count($this->StackEntity->load($all_pieces_args)),
				'loading using \'all\' did not return the expected '
				. 'number of entities');

		$all_formats_args = $this->StackEntity->accessArgs()
				->setLayer('formats')
				->setLimit('all');
        $this->assertEquals(
				2, 
				count($this->StackEntity->load($all_formats_args)),
				'loading using [\'all\'] did not return the expected '
				. 'number of entities');

		$first_piece_args = $this->StackEntity->accessArgs()
				->setLayer('pieces')
				->setLimit('first');
        $this->assertEquals(1, count($this->StackEntity->load($first_piece_args)),
				'loading using \'first\' did not return one entity');

		$first_format_args = $this->StackEntity
				->accessArgs()
				->setLayer('formats')
				->setLimit('first');
        $this->assertEquals(
				1, 
				count($this->StackEntity->load($first_format_args)),
				'loading using [\'first\'] did not return one entity');

        // unknown layer combinded with a field search
		$first_editionId_is_8_arg = $this->StackEntity->accessArgs()
				->setLimit('first')
				->setAccessNodeObject('value', 'edition_id')
				->filterValue(8);
        $this->assertEquals(
				0, 
				count($this->StackEntity->load($first_editionId_is_8_arg)),
				'loading using an unknow layer name and a property/value '
				. 'search returned something other than the 0 expected entities.');
    }
	
	public function testLoadWithArrayFilter() {
		$actual = $this->StackEntity->find()
				->setLayer('pieces')
				->specifyFilter('id', [40, 509], 'in_array')
				->load();
	}

// </editor-fold>
	
	/**
	 * Test element (a trait method) in StackEntity context
	 */
	public function testElement() {
		$this->assertTrue(is_a($this->StackEntity->element(0), '\App\Model\Entity\StackEntity'));
		$this->assertTrue($this->StackEntity->element(1) === null);
	}

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
        $this->assertTrue($this->StackEntity->exists('formats', 1));
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
    public function testRootLayerNamLayer()     {
        $this->assertEquals('artwork', $this->StackEntity->rootLayerName());
    }

    /**
     * Test primaryId method
     *
     * @return void
     */
    public function testRootId()     {
        $this->assertEquals(4, $this->StackEntity->rootID());
    }

    /**
     * Test primaryEntity method
     *
     * @return void
     */
    public function testrootElement()     {
        $entity = $this->StackEntity->rootElement();
        $this->assertInstanceOf('\App\Model\Entity\Artwork', $entity);
    }
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testRootEntityUnsetProperty() {
		$entity = New StackEntity();
        $entity->rootElement();
	}

    /**
     * Test distinct method
     *
     * @return void
     */
    public function testLoadDistinctWithGoodArgObj() {
		$actual = $this->StackEntity
			->find()
			->setLayer('pieces')
			->setAccessNodeObject('value', 'edition_id')
			->loadDistinct();
        $this->assertEquals([5, 8], $actual,
			'A valid layer and property did not return the expected values');
    }
	
	public function testTraitDistinct() {
		$result = $this->StackEntity
				->find()
				->setLayer('pieces')
				->specifyFilter('quantity', 2, '>')
				->load();
		$actual = $this->StackEntity->distinct('edition_id', $result);
		$this->assertArraySubset([8], $actual);
		
	}

    /**
     * Test IDs method
     *
     * @return void
     */
    public function testIDs()     {
		$this->assertTrue(
				$this->StackEntity->IDs() === [4], 
				'IDs() did not return the expected primary id for this stack');
        $this->assertEquals(
				[20, 38, 40, 509, 955], 
				$this->StackEntity->IDs('pieces'),
				'IDs() on a valid layer did not return the expected array of values');
        $this->assertEquals([4], $this->StackEntity->IDs('artwork'));
		$this->assertEmpty(
				$this->StackEntity->IDs('bad_layer'), 
				'IDs(badLayer) did not return an empty array');
    }

    /**
     * Test linkedTo method
     *
     * @return void
     */
    public function testLinkedTo()     {
        $unique = $this->StackEntity->linkedTo('edition', 5, 'pieces');
        $open = $this->StackEntity->linkedTo('edition', 8, 'pieces');
        $none = $this->StackEntity->linkedTo('link', 12, 'something');


        $this->assertEquals(1, count($unique), 'unique edition');
        $this->assertEquals(4, count($open), 'open edition');
        $this->assertEquals(0, count($none), 'nothing');
    
    }

	public function testDataOwner() {
		$actual = $this->StackEntity->dataOwner();
		$this->assertTrue('f22f9b46-345f-4c6f-9637-060ceacb21b2' === $actual);
	}
// </editor-fold>
    
// <editor-fold defaultstate="collapsed" desc="Modified parent methods">
    
    /**
     * Test extension of isEmpty method
     *
     * @return void
     */
    public function testIsEmpty(){
        $this->assertTrue(
				$this->StackEntity->isEmpty('member'), 
				'An uset property');
        $emptyLayer = new Layer([], 'addresses');
        $this->StackEntity->addresses = $emptyLayer;
        $this->assertTrue(
				$this->StackEntity->isEmpty('addresses'), 
				'An empty layer property, count = 0');
    }

    /**
     * Test set method extension
     *
     * @return void
     */
    public function testSet(){
        //extract an array, unset that value then 'set' the value
        //the set is a (string, value) arg arrangement
		$all_pieces_arg = $this->StackEntity
				->accessArgs()
				->setLayer('pieces')
				->setLimit('all');
        $pieces = $this->StackEntity->load($all_pieces_arg);
        $this->assertTrue(
				is_array($pieces), 
				'the load value is an array');
        
        unset($this->StackEntity->pieces);
        $this->assertTrue(
				$this->StackEntity->isEmpty('pieces'), 
				'piece value is gone');
        
        $this->StackEntity->set('pieces', $pieces);
        $this->assertInstanceOf(
				'\App\Model\Lib\Layer', 
				$this->StackEntity->get('pieces'), 
				'layer object was made');
        
        //do the same process to multiple values
        //to test the [prop=>val, prop=>val] arguement syntax
		$dp = $this->StackEntity->find()
				->setLayer('dispositions_pieces')
				->load();
        unset($this->StackEntity->pieces);
        unset($this->StackEntity->dispositions_pieces);
        
        $this->assertTrue(
				$this->StackEntity->isEmpty('pieces'), 
				'piece value is gone');
        $this->assertTrue(
				$this->StackEntity->isEmpty('dispositions_pieces'), 
				'piece value is gone');
        
        $this->StackEntity->set([
			'pieces' => $pieces, 
			'dispositions_pieces' => $dp, 
			'something' => ['array']]);
        
        $this->assertInstanceOf(
				'\App\Model\Lib\Layer', 
				$this->StackEntity->get('pieces'), 
				'layer object was made');
        $this->assertInstanceOf(
				'\App\Model\Lib\Layer', 
				$this->StackEntity->get('dispositions_pieces'), 
				'layer object was made');
        $this->assertTrue(
				is_array($this->StackEntity->get('something')),
				'array was set');
        
        //do the same process to multiple values and use the guard feature
        //to test the [prop=>val, prop=>val] argument syntax
//        $pieces = $this->StackEntity->load('pieces', 'all');
//        $dp = $this->StackEntity->load('dispositions_pieces', 'all');
//        unset($this->StackEntity->pieces);
//        unset($this->StackEntity->dispositions_pieces);
//        
//        $this->assertTrue($this->StackEntity->isEmpty('pieces'), 'piece value is gone');
//        $this->assertTrue($this->StackEntity->isEmpty('dispositions_pieces'), 'piece value is gone');
//        
//        $this->StackEntity->set(['pieces' => $pieces, 'dispositions_pieces' => $dp, 'something' => ['array']]);
//        
//        $this->assertInstanceOf('\App\Model\Lib\Layer', $this->StackEntity->get('pieces'), 'layer object was made');
//        $this->assertInstanceOf('\App\Model\Lib\Layer', $this->StackEntity->get('dispositions_pieces'), 'layer object was made');
//        $this->assertTrue(is_array($this->StackEntity->get('something')), 'array was set');
    }

// </editor-fold>
    
// <editor-fold defaultstate="collapsed" desc="Inherited from entity">
    
    /**
     * Test has method
     *
     * @return void
     */
    public function testGet(){
        $this->assertInstanceOf(
				'\App\Model\Lib\Layer', 
				$this->StackEntity->get('pieces'));
        $this->assertEquals(null, $this->StackEntity->get('members'));
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

// </editor-fold>

}

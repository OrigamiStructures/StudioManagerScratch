<?php

namespace App\Test\TestCase\Model\Lib;

// from StackEntityTest
use App\Model\Table\ArtStacksTable;
use Cake\ORM\TableRegistry;
use App\Model\Entity\StackEntity;
use App\Model\Lib\Layer;
use App\ORM\Entity\Address;
use App\Model\Lib\StackSet;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Lib\StackSet Test Case
 */
class StackSetTest extends TestCase {

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
	 * The StackSet for testing
	 *
	 * @var App\Model\Lib\StackSet
	 */
	public $StackSet;

	/**
	 * The StackEntities for inclusion in the set
	 *
	 * @var \App\Model\Entity\StackEntity
	 */
	public $StackEntities;

	/**
	 * The table object
	 *
	 * @var \App\Model\Table\ArtStacksTable
	 */
	public $ArtStacks;

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->ArtStacks = TableRegistry::getTableLocator()->get('ArtStacks', []);
		$artIDs = [
			4, //jabberwocky
			5, //global warming survival kit
		];
		$this->StackEntities = $this->ArtStacks->find('stacksFor',
				['seed' => 'artworks', 'ids' => $artIDs]);
//        $this->StackEntity = $stacks->ownerOf('artwork', $artID, 'first');
//        $this->StackEntity->arrayProp = ['a','b','c'];
//        $this->StackEntity->stringProp = 'This is a string property';
//        $this->StackEntity->numProp = 498;
		//art 4, ed 5 Unique qty 1, ed 8 Open Edition qty 150
		//fmt 5 desc Watercolor 6 x 15", fmt 8 desc Digital output with cloth-covered card stock covers
		//pc 20 nm null qty 1, pc 38,40,509,955 qty 140,7,1,2
		//art 5, ed 6 Limited Edition qty 15, ed 20 Unique qty 1
		//fmt 6 desc "Paper covered container with 4 trays. Trays display mounted and 
		//			  lacquered digital content on the front and QR codes which link 
		//			  web addresses on the reverse", 
		//fmt 8 desc "Prototype made while developing edition details.
		//			  Paper covered container with 4 trays. Trays display mounted and lacquered digital 
		//			  content on the front and QR codes which link web addresses on the reverse.""
		//pc 21-26,500-507 numb 1-15 qty 1each, pc 508 numb null qty 1

	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->StackEntities);
		unset($this->ArtStacks);

		parent::tearDown();
	}

	/**
	 * Test find method
	 *
	 * @return void
	 */
	public function testFind() {
        $arg = $this->StackEntities->element(1)->find();
        $this->assertTrue(is_a($arg, 'App\Model\Lib\LayerAccessArgs'),
            'find() did not create a LayerAccessArgs object');
        $this->assertTrue(is_a($arg->data(), 'App\Model\Entity\StackEntity'),
            'The access object created by find() did not contain the expected data');
	}
	
	/**
	 * Test find method with provided layer argument
	 *
	 * @return void
	 */
	public function testFindWithLayer() {
        $arg = $this->StackEntities->element(1)->find('layerChoice');
        $this->assertTrue(is_a($arg, 'App\Model\Lib\LayerAccessArgs'),
            'find() did not create a LayerAccessArgs object');
		
        $this->assertTrue(is_a($arg->data(), 'App\Model\Entity\StackEntity'),
            'The access object created by find() did not contain the expected data');
		
		$this->assertTrue($arg->valueOf('layer') === 'layerChoice', 
			'The access object created with the \'layer\' option '
			. 'did not have a layer set');
	}

// <editor-fold defaultstate="collapsed" desc="LAYER ACCESS INTERFACE REALIZATON">

// <editor-fold defaultstate="collapsed" desc="Load method tests">
    
    public function testLoadDirectlyOnStackSet() {
        $this->assertTrue(is_array($this->StackEntities->load()),
            'load() did not return an array as expected');
        $this->assertArrayHasKey(4, $this->StackEntities->load(), 
            'load() didn\'t have an expected key (an item ID)');
    }

    public function testLoadHandFiltering() {
        
        $pieces = $this->StackEntities->find()
            ->setLayer('pieces')
			->specifyFilter('quantity', 140)
//            ->setAccessNodeObject('filter', 'quantity')
//            ->filterValue(140)
            ->load();
//		debug($pieces);
        $piece = array_shift($pieces);
        $this->assertEquals(140, $piece->quantity,
				'loading a valid format by property/value test ...->load(\'pieces\', [\'quantity\', 140])... '
				. 'did not return an entity with an expected property value.');
    }
	
	public function testLoadWithBadStringLayerName() {
        $pieces = $this->StackEntities->load('unknown');
        $this->assertEquals(0, count($pieces),
				'direct load(\'badName\') did not return the expected '
				. 'empty array');
	}
    
	public function testLoadWithStringLayerName() {
        $pieces = $this->StackEntities->load('pieces');
        $this->assertEquals(21, count($pieces),
				'direct load(\'layerName\') did not return the expected '
				. 'number of entities');
	}
    
    public function testLoadAllOfLayer() {
        
        $pieces = $this->StackEntities->find()
            ->setLimit('all')
            ->setLayer('pieces')
            ->load();
        $this->assertEquals(21, count($pieces),
				'loading using \'all\' did not return the expected number of entities');

        
        $formats = $this->StackEntities->find()
            ->setLimit('all')
            ->setLayer('formats')
            ->load();
        $this->assertEquals(4, count($formats),
				'loading using [\'all\'] did not return the expected number of entities');
        
    }
    /**
     * Test load first of a layer from each stack method
     *
     * @return void
     */
    public function testLoadFirstOfLayer() {
        
        $piece = $this->StackEntities->find()
            ->setLimit('first')
            ->setLayer('pieces')
            ->load();
        $this->assertEquals(2, count($piece),
				'loading using \'first\' did not return the first '
				. 'from each Entity in the stack');

        $format = $this->StackEntities->find()
            ->setLimit(1)
            ->setLayer('formats')
            ->load();
        $this->assertEquals(2, count($format),
				'loading using [\'first\'] did not return one entity');
    }

// </editor-fold>

	
	/**
	 * Test element (a trait method) in StackSet context
	 */
	public function testElement() {
		$this->assertTrue($this->StackEntities->element(1)->rootID() === 5);
		$this->assertTrue($this->StackEntities->element(2) === null);
	}
	
	public function testIDs() {
		$this->assertTrue($this->StackEntities->IDs() === [4, 5], 'IDs() did '
				. 'not return the IDs of the primary entities of the set');
		$this->assertEmpty($this->StackEntities->IDs('badLayer'), 'IDs(badLayer) '
				. 'did not return the expected empty array');
		$this->assertTrue($this->StackEntities->IDs('editions') === [5,8,6,20], 
				'IDs(editions) did not return the expected 4 IDs');
	}
	
	/**
	 * Test distinct method
	 *
	 * @return void
	 */
	public function testLoadDistinct() {
		$this->assertEquals(["Unique","Open Edition","Limited Edition"], 
				$this->StackEntities
				->find()
				->setLayer('editions')
				->setAccessNodeObject('value', 'type')
				->loadDistinct(),
				'Distinct did not return the expected set of edition types '
				. 'from a set of stack entities');
		
	}

	public function testTraitDistinct() {
		$result = $this->StackEntities
				->find()
				->setLayer('editions')
				->load();
		$actual = $this->StackEntities->distinct('type', $result);
		$this->assertEquals(
			["Unique","Open Edition","Limited Edition"], 
			$actual,
			'Distinct did not return the expected set of edition types '
			. 'from a set of stack entities');
	}

	/**
	 * Test linkedTo method
	 *
	 * @return void
	 */
	public function testLinkedTo() {

        $unique = $this->StackEntities->linkedTo('edition', 5, 'pieces');
        $open = $this->StackEntities->linkedTo('edition', 8, 'pieces');
        $none = $this->StackEntities->linkedTo('link', 12, 'something');


        $this->assertEquals(1, count($unique), 'unique edition 5 should have 1 piece');
        $this->assertEquals(4, count($open), 'open edition 8 should have 4 pieces');
        $this->assertEquals(0, count($none), 'nothing, bad layer and foreign, has no pieces');
	}
	
	/**
	 * Layer content for multiple stacks should accumulate
	 */
	public function testLoadLayerContentAccumulation() {
		$this->assertCount(4, $this->StackEntities
				->find()
				->setLayer('editions')
				->load());
	}
	
	/**
	 * Tests two streamlined calls
	 * 
	 * both the `find( )` and `loadValueList( )` variants that 
	 * accept arguments to eliminate the `set` calls are used here
	 */
	public function testFindAndLoadValueListWithArguments() {
		$list = $this->StackEntities
				->find('formats')
				->loadValueList('edition_id');
		$this->assertArraySubset([5,8,6,20], $list,
				'either find( ) with a layer argument or loadValueList( )'
				. 'with a node-name argument didn\'t return the expected list');
	}
	
	public function testFindKeyValueListWithArguements() {
		$list = $this->StackEntities
				->find('formats')
				->loadKeyValueList('edition_id', 'edition_id');
		$this->assertArraySubset([5=>5,8=>8,6=>6,20=>20], $list,
				'either find( ) with a layer argument or loadKeyValueList( )'
				. 'with arguments didn\'t return the expected list');
	}

// </editor-fold>
}

class Produce extends \Cake\ORM\Entity {
	
}

class Group extends \Cake\ORM\Entity {
	
}

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
		$this->StackEntities = $this->ArtStacks->find('stackFrom',
				['layer' => 'artworks', 'ids' => $artIDs]);
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
	 * Test insert method
	 *
	 * @return void
	 */
//	public function testInsert() {
//		$this->markTestIncomplete('Not implemented yet.');
//	}

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

//	/**
//	 * Test members method
//	 *
//	 * @return void
//	 */
//	public function testMembers() {
//		$this->markTestIncomplete('Not implemented yet.');
//	}
//
//	/**
//	 * Test element method
//	 *
//	 * @return void
//	 */
//	public function testElement() {
//		$this->markTestIncomplete('Not implemented yet.');
//	}
//
//	/**
//	 * Test member method
//	 *
//	 * @return void
//	 */
//	public function testMember() {
//		$this->markTestIncomplete('Not implemented yet.');
//	}
//
//	/**
//	 * Test count method
//	 *
//	 * @return void
//	 */
//	public function testCount() {
//		$this->markTestIncomplete('Not implemented yet.');
//	}
//
//	/**
//	 * Test isMember method
//	 *
//	 * @return void
//	 */
//	public function testIsMember() {
//		$this->markTestIncomplete('Not implemented yet.');
//	}
//
//	/**
//	 * Test ownerOf method
//	 *
//	 * @return void
//	 */
//	public function testOwnerOf() {
//		$this->markTestIncomplete('Not implemented yet.');
//	}
//
//	/**
//	 * Test IDs method
//	 *
//	 * @return void
//	 */
//	public function testIDs() {
//		$this->markTestIncomplete('Not implemented yet.');
//	}

// <editor-fold defaultstate="collapsed" desc="LAYER ACCESS INTERFACE REALIZATON">

// <editor-fold defaultstate="collapsed" desc="Load method tests">

    public function testLoadIndexItemFromLayerNew() {
        
        $formats = $this->StackEntities->find()
            ->setLayer('formats')
            ->setIdIndex(5)
            ->load();
        
//		var_dump($formats);
        $format = array_shift($formats);
//		var_dump($format);
        $this->assertEquals('Watercolor 6 x 15"', $format->description,
				'loading a valid format by exposed id ...->load(\'formats\', 5)... '
				. 'did not return an entity with an expected property value.');

        $formats = $this->StackEntities->find()
            ->setLayer('formats')
            ->setIdIndex(8)
            ->load();
        
        $format = array_shift($formats);
        $this->assertStringStartsWith('Digital output', $format->description,
				'loading a valid format by array value ...->load(\'formats\', 8)... '
				. 'did not return an entity with an expected property value.');
    }

    public function testLoadIndexItemFromLayerOld() {
		$format_index_5_arg = $this->StackEntities->accessArgs()
				->setLayer('formats')
				->setIdIndex(5);
        $formats = $this->StackEntities->load($format_index_5_arg);
//		var_dump($formats);
		$format = array_shift($formats);
//		var_dump($format);
        $this->assertEquals('Watercolor 6 x 15"', $format->description,
				'loading a valid format by exposed id ...->load(\'formats\', 5)... '
				. 'did not return an entity with an expected property value.');

		$argObj = $this->StackEntities->accessArgs()
				->setLayer('formats')
				->setIdIndex(8);
        $formats = $this->StackEntities->load($argObj);
		$format = array_shift($formats);
        $this->assertStringStartsWith('Digital output', $format->description,
				'loading a valid format by array value ...->load(\'formats\', 8)... '
				. 'did not return an entity with an expected property value.');
    }

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

		$argObj = $this->StackEntities->accessArgs()
				->setLayer('pieces')
				->setValueSource('quantity')
				->filterValue(140);
        $pieces = $this->StackEntities->load($argObj);
//		pr($this->StackEntities);
//		pr($pieces);
        $piece = array_shift($pieces);
        $this->assertEquals(140, $piece->quantity,
				'loading a valid format by property/value test ...->load(\'pieces\', [\'quantity\', 140])... '
				. 'did not return an entity with an expected property value.');

		$argObj = $this->StackEntities->accessArgs()->setLimit('all')
				->setLayer('pieces');
        $this->assertEquals(21, count($this->StackEntities->load($argObj)),
				'loading using \'all\' did not return the expected number of entities');

		$argObj = $this->StackEntities->accessArgs()->setLimit('all')
				->setLayer('formats');
        $this->assertEquals(4, count($this->StackEntities->load($argObj)),
				'loading using [\'all\'] did not return the expected number of entities');

		$argObj = $this->StackEntities->accessArgs()
				->setLimit('first')
				->setLayer('pieces');
        $this->assertEquals(2, count($this->StackEntities->load($argObj)),
				'loading using \'first\' did not return the first '
				. 'from each Entity in the stack');

		$argObj = $this->StackEntities->accessArgs()
				->setLimit(1)
				->setLayer('formats');
        $this->assertEquals(2, count($this->StackEntities->load($argObj)),
				'loading using [\'first\'] did not return one entity');
    }

// </editor-fold>

	
	/**
	 * Test element (a trait method) in StackSet context
	 */
	public function testElement() {
		$this->assertTrue($this->StackEntities->element(1)->primaryId() === 5);
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
	public function testDistinct() {
		$this->assertEquals(["Unique","Open Edition","Limited Edition"], 
				$this->StackEntities->distinct('type', 'editions'),
				'Distinct did not return the expected set of edition types '
				. 'from a set of stack entities');
		
		$this->assertEmpty($this->StackEntities->distinct('type', 'badLayer'),
				'Distinct did not return an empty array when passed a bad layer');
		
		$this->assertEmpty($this->StackEntities->distinct('garbage', 'editions'),
				'Distinct did not return an empty array when passed a bad property');
	}

//	/**
//	 * Test filter method
//	 *
//	 * @return void
//	 */
//	public function testFilter() {
//		$this->markTestIncomplete('Not implemented yet.');
//	}
//
//	/**
//	 * Test keyedList method
//	 *
//	 * @return void
//	 */
//	public function testKeyedList() {
//		$this->markTestIncomplete('Not implemented yet.');
//	}

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

// </editor-fold>
}

class Produce extends \Cake\ORM\Entity {
	
}

class Group extends \Cake\ORM\Entity {
	
}

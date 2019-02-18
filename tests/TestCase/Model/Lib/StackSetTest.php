<?php

namespace App\Test\TestCase\Model\Lib;

// from StackEntityTest
use App\Model\Table\ArtStacksTable;
use Cake\ORM\TableRegistry;
use App\Model\Entity\StackEntity;
use App\Lib\Layer;
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
//
//	/**
//	 * Test all method
//	 *
//	 * @return void
//	 */
//	public function testAll() {
//		$this->markTestIncomplete('Not implemented yet.');
//	}
//
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
		$argObj = $this->StackEntities->accessArgs();
        $formats = $this->StackEntities->load('formats', 5, $argObj);
//		var_dump($formats);
		$format = array_shift($formats);
//		var_dump($format);
        $this->assertEquals('Watercolor 6 x 15"', $format->description,
				'loading a valid format by exposed id ...->load(\'formats\', 5)... '
				. 'did not return an entity with an expected property value.');

		$argObj = $this->StackEntities->accessArgs();
        $formats = $this->StackEntities->load('formats', [8], $argObj);
		$format = array_shift($formats);
        $this->assertStringStartsWith('Digital output', $format->description,
				'loading a valid format by array value ...->load(\'formats\', [8])... '
				. 'did not return an entity with an expected property value.');

		$argObj = $this->StackEntities->accessArgs();
        $pieces = $this->StackEntities->load('pieces', ['quantity', 140], $argObj);
        $piece = array_shift($pieces);
        $this->assertEquals(140, $piece->quantity,
				'loading a valid format by property/value test ...->load(\'pieces\', [\'quantity\', 140])... '
				. 'did not return an entity with an expected property value.');

		$argObj = $this->StackEntities->accessArgs()->limit('all');
        $this->assertEquals(21, count($this->StackEntities->load('pieces', '', $argObj)),
				'loading using \'all\' did not return the expected number of entities');

		$argObj = $this->StackEntities->accessArgs()->limit('all');
        $this->assertEquals(4, count($this->StackEntities->load('formats', [''], $argObj)),
				'loading using [\'all\'] did not return the expected number of entities');

		$argObj = $this->StackEntities->accessArgs()->limit('first')->property('pieces');
        $this->assertEquals(2, count($this->StackEntities->load('pieces', '', $argObj)),
				'loading using \'first\' did not return one entity');

		$argObj = $this->StackEntities->accessArgs()->limit(1)->property('formats');
        $this->assertEquals(2, count($this->StackEntities->load('formats', [''], $argObj)),
				'loading using [\'first\'] did not return one entity');

        // unknown layer combinded with a field search
		$argObj = $this->StackEntities->accessArgs();
        $this->assertEquals(0, count($this->StackEntities->load('gizmo', ['edition_id', 8], $argObj)),
				'loading using an unknow layer name and a property/value search returned something '
				. 'other than the 0 expected entities.');
    }

// </editor-fold>

	/**
	 * Test distinct method
	 *
	 * @return void
	 */
//	public function testDistinct() {
//		$this->markTestIncomplete('Not implemented yet.');
//	}
//
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
		$this->markTestIncomplete('Not implemented yet.');
	}

// </editor-fold>
}

class Produce extends \Cake\ORM\Entity {
	
}

class Group extends \Cake\ORM\Entity {
	
}

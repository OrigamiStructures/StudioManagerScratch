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
		'app.series',
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

// <editor-fold defaultstate="collapsed" desc="LAYER ACCESS INTERFACE REALIZATON">

	/**
	 * Test element (a trait method) in StackSet context
	 */
	public function testElement() {
		$this->assertTrue($this->StackEntities->element(1)->rootID() === 5);
		$this->assertTrue($this->StackEntities->element(2) === null);
	}

    /**
     * @todo Test unkown layer, should return empty array
     */
	public function testIDs() {
		$this->assertTrue($this->StackEntities->IDs() === [4, 5], 'IDs() did '
				. 'not return the IDs of the primary entities of the set');
		$this->assertTrue($this->StackEntities->IDs('editions') === [5,8,6,20],
				'IDs(editions) did not return the expected 4 IDs');
	}

    /**
     */
    public function testIDsOnBadLayer()
    {
		$this->assertEquals([], $this->StackEntities->IDs('badLayer'));
	}
	/**
	 * Test linkedTo method
	 *
	 * @return void
	 */
	public function testLinkedTo() {

        $unique = $this->StackEntities->linkedTo('edition', 5, 'pieces')->toArray();
        $open = $this->StackEntities->linkedTo('edition', 8, 'pieces')->toArray();
//        $none = $this->StackEntities->linkedTo('link', 12, 'something')->toArray();


        $this->assertEquals(1, count($unique), 'unique edition 5 should have 1 piece');
        $this->assertEquals(4, count($open), 'open edition 8 should have 4 pieces');
//        $this->assertEquals(0, count($none), 'nothing, bad layer and foreign, has no pieces');
	}

// </editor-fold>
}

class Produce extends \Cake\ORM\Entity {

}

class Group extends \Cake\ORM\Entity {

}

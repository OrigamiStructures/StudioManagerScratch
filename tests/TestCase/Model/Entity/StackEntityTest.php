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
		'app.series',
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
		$this->StackEntity = $stacks->element(0, LAYERACC_INDEX);

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
     * Test IDs method
     *
     * @todo test bad layer name, should return empty array
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
    }

    /**
     * @expectedException Error
     */
    public function testIDsOnBadLayer()
    {
        $this->StackEntity->IDs('bad_layer');
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
        $pieces = $this->StackEntity
            ->getLayer('pieces')
            ->NEWfind()
            ->toArray();
        unset($this->StackEntity->pieces);
        $this->StackEntity->set('pieces', $pieces);

        $this->assertInstanceOf(
				'\App\Model\Lib\Layer',
				$this->StackEntity->get('pieces'),
				'set()ing a layer type column with and array of '
				. 'entities did not produce a Layer object.');

        //do the same process to multiple values
        //to test the [prop=>val, prop=>val] arguement syntax
		$dp = $this->StackEntity
            ->getLayer('dispositions_pieces')
            ->toArray();
        unset($this->StackEntity->pieces);
        unset($this->StackEntity->dispositions_pieces);

        $this->StackEntity->set([
			'pieces' => $pieces,
			'dispositions_pieces' => $dp,
			'something' => ['array']]);

        $this->assertInstanceOf(
				'\App\Model\Lib\Layer',
				$this->StackEntity->get('pieces'),
				'set()ing a layer type column with and array of '
				. 'entities did not produce a Layer object.');
        $this->assertInstanceOf(
				'\App\Model\Lib\Layer',
				$this->StackEntity->get('dispositions_pieces'),
				'set()ing a layer type column with and array of '
				. 'entities did not produce a Layer object.');
        $this->assertTrue(
				is_array($this->StackEntity->get('something')),
				'set()ing an arbitrary column to an array did '
				. 'not give the entity\'s property an array value');
    }

	/**
	 * Using this two-argument form of StackEntity->set() works fine
	 * but the marshalling system for stackEntities uses a different
	 * variaiton, one arg = ['pieces' => []] and this variant
	 * caused problems when the value of the node was an empty array.
	 * That's why the tests were passing though the code was failing.
	 */
	public function testSetLayerColumnToEmptyArray() {
        unset($this->StackEntity->pieces);
        $this->StackEntity->set('pieces', []);

        $this->assertInstanceOf(
				'\App\Model\Lib\Layer',
				$this->StackEntity->get('pieces'),
				'set()ing a layer type column with an emptynarray '
				. 'did not produce a Layer object when using ->get($property)');

        $this->assertInstanceOf(
				'\App\Model\Lib\Layer',
				$this->StackEntity->pieces,
				'set()ing a layer type column with an emptynarray '
				. 'did not produce a Layer object when using ->$property');

	}

	/**
	 * This test uses the alternate ->set([$key => []]) variant
	 */
	public function testSetLayerColumnToEmptyArrayAlt() {
        unset($this->StackEntity->pieces);
        $this->StackEntity->set(['pieces' => []]);

        $this->assertInstanceOf(
				'\App\Model\Lib\Layer',
				$this->StackEntity->get('pieces'),
				'set([])ing a layer type column with an emptynarray '
				. 'did not produce a Layer object when using ->get($property)');

        $this->assertInstanceOf(
				'\App\Model\Lib\Layer',
				$this->StackEntity->pieces,
				'set([])ing a layer type column with an emptynarray '
				. 'did not produce a Layer object when using ->$property');

	}

	public function testMethodOnEmptyLayer() {
        unset($this->StackEntity->pieces);
        $this->StackEntity->set('pieces', []);

		$this->assertTrue(is_array($this->StackEntity->IDs('pieces')));
		$this->assertTrue(empty($this->StackEntity->IDs('pieces')));
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

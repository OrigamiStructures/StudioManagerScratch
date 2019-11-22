<?php
namespace App\Test\TestCase\Model\Lib;

use App\Model\Lib\Layer;
use Cake\ORM\TableRegistry;
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
        $this->Pieces = TableRegistry::getTableLocator()->get('Pieces');
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
        $this->assertInstanceOf('App\Model\Lib\Layer', $layer,
            'Creation with an empty array does not produce a '
            . 'Layer object');

        $layer = new Layer([$record], 'edition');
        $this->assertInstanceOf('App\Model\Lib\Layer', $layer,
            'Creation with records in an array, plus a matching '
            . 'entity name does not produce a Layer object.');

        $layer = new Layer([$record]);
        $this->assertInstanceOf('App\Model\Lib\Layer', $layer,
            'Creation with an array of records does not '
            . 'produce a Layer object');
    }


    /**
     * One of the two args must give a way to identify the layer type
     *
     * @expectedException App\Exception\BadClassConfigurationException
     */
    public function testNoArgInitialization()
    {
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
    public function testNotEntityInitialization()
    {
        $notEntityObj = new \stdClass();

        $this->expectExceptionMessageRegExp('/only accept objects that extend Entity/');
        $layer = new Layer([$notEntityObj], null);
    }

    /**
     * All entities must be the same class
     *
     * @expectedException App\Exception\BadClassConfigurationException
     */
    public function testMixedEntityInitialization()
    {
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
    public function testMissingIdInitialization()
    {
        $art = $this->artRecord[0];
        unset($art->id);

        $this->expectExceptionMessageRegExp('/expects to find \$entity->id/');
        $layer = new Layer([$art], null);
    }

    public function testMembersFromTheTrait()
    {
        $layer = new Layer($this->fivePieces);
        $this->assertCount(5, $layer->IDs(),
            'IDs() did not return an array of the expected size');
    }

    public function testMemberFromTheTrait()
    {
        $layer = new Layer($this->fivePieces);
        $this->assertInstanceOf('App\Model\Entity\Piece', $layer->element(962, LAYERACC_ID),
            'element(x, LAYERACC_ID) did not return a piece entity');
    }

    /**
     * test name property
     */
    public function testLayerName()
    {
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
    public function testEntityClass()
    {
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
    public function testCount()
    {
        $layer = new Layer([], 'edition');
        $this->assertEquals(0, $layer->count());

        $layer = new Layer($this->pieceRecords);
        $this->assertEquals(53, $layer->count());

        $layer = new Layer($this->artRecord);
        $this->assertEquals(1, $layer->count());
    }

    public function testIDs()
    {
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

    public function testHas()
    {
        $layer = new Layer($this->fivePieces);

        $this->assertTrue($layer->hasId(965));
        $this->assertTrue($layer->hasId('962'));
        $this->assertFalse($layer->hasId(3));
        $this->assertFalse($layer->hasId('something wrong'));
    }

    /**
     * Test element (a trait method) in Layer context
     */
    public function testElement()
    {
        $layer = new Layer($this->fivePieces);
        $this->assertTrue($layer->element(0)->id === 961);
        $this->assertTrue($layer->element(6) === null);
    }

    /**
     * Check that no entities have changed
     */
    public function testIsClean()
    {
        $layer = new Layer($this->fivePieces);
        $this->assertTrue($layer->isClean());

        $piece = new \App\Model\Entity\Piece(['id' => 400000]);
        $layer = new Layer([$piece]);
        $this->assertFalse($layer->isClean());
    }

    /**
     * Test access to belongsTo sets
     */
    public function testLinkedTo()
    {
        $layer = new Layer($this->pieceRecords);
        $this->assertEquals(5, count($layer->linkedTo('format', 36)->toArray()));
        $this->assertEquals(40, count($layer->linkedTo('format', null)->toArray()));
        $this->assertEquals(0, count($layer->linkedTo('format', 500)->toArray()));
        $this->assertEquals(0, count($layer->linkedTo('junk', 36)->toArray()));
    }

}

<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Table\ArtStacksTable;
use Cake\ORM\TableRegistry;
use App\Model\Entity\StackEntity;
use Cake\TestSuite\TestCase;

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

    /**
     * Test exists method
     * 
     * @return void
     */
    public function testExists()
    {
        $this->assertFalse($this->StackEntity->exists('artwork', 50));
        $this->assertFalse($this->StackEntity->exists('something', 6));
        $this->assertTrue($this->StackEntity->exists('artwork', 4));
        $this->assertTrue($this->StackEntity->exists('editions', 8));
        $this->assertTrue($this->StackEntity->exists('formats', 5));
        $this->assertTrue($this->StackEntity->exists('pieces', 955));
    }
    
    /**
     * Test load method
     *
     * @return void
     */
    public function testLoad()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test count method
     *
     * @return void
     */
    public function testCount()
    {
        $this->assertEquals(1, $this->StackEntity->count('artwork'));
        $this->assertEquals(5, $this->StackEntity->count('pieces'));
        $this->assertEquals(2, $this->StackEntity->count('formats'));
    }

    /**
     * Test hasNo method
     *
     * @return void
     */
    public function testHasNo()
    {
        $this->assertFalse($this->StackEntity->hasNo('editions'), 'has no editions');
        $this->assertTrue($this->StackEntity->hasNo('members'), 'has no members');
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testHas()
    {
        $this->assertTrue($this->StackEntity->has('editions'), 'has editions');
        $this->assertFalse($this->StackEntity->has('members'), 'has members');
    }

    /**
     * Test primaryLayer method
     *
     * @return void
     */
    public function testPrimaryLayer()
    {
        $this->assertEquals('artwork', $this->StackEntity->primaryLayer());
    }

    /**
     * Test primaryId method
     *
     * @return void
     */
    public function testPrimaryId()
    {
        $this->assertEquals(4, $this->StackEntity->primaryId());
    }

    /**
     * Test primaryEntity method
     *
     * @return void
     */
    public function testPrimaryEntity()
    {
        $entity = $this->StackEntity->primaryEntity();
        $this->assertInstanceOf('\App\Model\Entity\Artwork', $entity);
    }

    /**
     * Test distinct method
     *
     * @return void
     */
    public function testDistinct()
    {
        $this->assertEquals([5,8], $this->StackEntity->distinct('pieces', 'edition_id'));
        $this->assertEquals([5,8], $this->StackEntity->distinct('formats', 'edition_id'));
    }

    /**
     * Test IDs method
     *
     * @return void
     */
    public function testIDs()
    {
        $this->assertEquals([20,38,40,509,955], $this->StackEntity->IDs('pieces'));
        $this->assertEquals([4], $this->StackEntity->IDs('artwork'));
    }

    /**
     * Test linkedTo method
     *
     * @return void
     */
    public function testLinkedTo()
    {
        $unique = $this->StackEntity->linkedTo('pieces', ['edition_id', 5]);
        $open = $this->StackEntity->linkedTo('pieces', ['edition_id', 8]);
        $none = $this->StackEntity->linkedTo('something', ['link', 12]);
        
        $this->assertEquals(1, count($unique), 'unique edition');
        $this->assertEquals(4, count($open), 'open edition');
        $this->assertEquals(0, count($none), 'nothing');
        
    }
}

<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ArtStacksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ArtStacksTable Test Case
 */
class ArtStacksTableTest extends TestCase
{

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
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ArtStacks);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertArraySubset([
                'id' => 'integer',
                'artwork' => 'layer',
                'editions' => 'layer',
                'formats' => 'layer',
                'pieces' => 'layer',
                'dispositionsPieces' => 'layer'
            ], $this->ArtStacks->getSchema()->typeMap());
    }

    /**
     * Test __get method
     * 
     * @dataProvider TableClassesProvider
     *
     * @return void
     */
    public function testGet($className, $propertyName)
    {
        $this->assertInstanceOf($className, $this->ArtStacks->$propertyName);
    }
    
    public function TableClassesProvider() {
       return [
           ['App\Model\Table\ArtworksTable', 'Artworks'],
           ['App\Model\Table\EditionsTable', 'Editions'],
           ['App\Model\Table\FormatsTable', 'Formats'],
           ['App\Model\Table\PiecesTable', 'Pieces'],
       ];
    }

    /**
     * Test findStackFrom method
     *
     * @return void
     */
    public function testFindStackFrom()
    {
        $this->markTestIncomplete('Not implemented yet.');
        
    }
    
    /**
     * Test findStackFrom method
     * 
     * @dataProvider stackSeedLayerVariantProvider
     *
     * @return void
     */
    public function testFindStackFromLayerVariants($args, $art, $ed, $fo, $p_cnt, $d_cnt)
    {
        $stacks = $this->ArtStacks->find('stackFrom', $args);
        $this->assertEquals(1, $stacks->count());
        $entity = $stacks->owner('artwork', $art)[0];
        
        $this->assertTrue($entity->exists('editions', $ed), "===\nedition is $ed\n===");
        $this->assertTrue($entity->exists('formats', $fo), "===\nformat is $fo\n===");
        $this->assertEquals($p_cnt, $entity->count('pieces'));
        $this->assertEquals($d_cnt, $entity->count('dispositionsPieces'));
    }
    
    public function stackSeedLayerVariantProvider() {
        return [
        // george art13 ed21 fmt22 - 15 pieces 510-524 - 8 disp-id (12-15,48,51)
            'disposition for George' => [
                ['layer' => 'disposition', 'ids' => [12,14,48]],
                13, 21, 22, 15, 8 //art-id, ed-id, form-id, piec-cnt, disp-cnt
            ],
            'dispositions for George' => [
                ['layer' => 'dispositions', 'ids' => [12,14,48]],
                13, 21, 22, 15, 8 //art-id, ed-id, form-id, piec-cnt, disp-cnt
            ],
        // george art13 ed21 fmt22 - 15 pieces 510-524 - 8 pc-id 8 disp-id (12-15,48,51)
            'piece for George' => [
                ['layer' => 'piece', 'ids' => [510,520]],
                13, 21, 22, 15, 8 //art-id, ed-id, form-id, piec-cnt, disp-cnt
            ],
            'pieces for George' => [
                ['layer' => 'pieces', 'ids' => [510]],
                13, 21, 22, 15, 8 //art-id, ed-id, form-id, piec-cnt, disp-cnt
            ],
            'format for George' => [
                ['layer' => 'format', 'ids' => [22]],
                13, 21, 22, 15, 8 //art-id, ed-id, form-id, piec-cnt, disp-cnt
            ],
            'formats for George' => [
                ['layer' => 'formats', 'ids' => [22]],
                13, 21, 22, 15, 8 //art-id, ed-id, form-id, piec-cnt, disp-cnt
            ],
            'edition for George' => [
                ['layer' => 'edition', 'ids' => [21]],
                13, 21, 22, 15, 8 //art-id, ed-id, form-id, piec-cnt, disp-cnt
            ],
            'editions for George' => [
                ['layer' => 'editions', 'ids' => [21]],
                13, 21, 22, 15, 8 //art-id, ed-id, form-id, piec-cnt, disp-cnt
            ],
            'artwork for George' => [
                ['layer' => 'artwork', 'ids' => [13]],
                13, 21, 22, 15, 8 //art-id, ed-id, form-id, piec-cnt, disp-cnt
            ],
            'artworks for George' => [
                ['layer' => 'artworks', 'ids' => [13]],
                13, 21, 22, 15, 8 //art-id, ed-id, form-id, piec-cnt, disp-cnt
            ],
        ];
    }

    /**
     * Test findStackFrom method
     * 
     * @expectedException BadMethodCallException
     *
     * @return void
     */
    public function testFindStackFromUnknownLayer()
    {
        $this->expectExceptionMessage("ArtStacks can't do lookups");
        $this->ArtStacks->find('stackFrom', 
                ['layer' => 'unkown', 'ids' => [4,5,6]]);
    }

    /**
     * Test stacksFromAtworks method
     *
     * @return void
     */
    public function testStacksFromAtworks()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test _marshall method
     *
     * @return void
     */
    public function testMarshall()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

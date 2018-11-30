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
        'app.art_stacks',
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
            ], $this->ArtStacks->getSchema());
    }

    /**
     * Test __get method
     *
     * @return void
     */
    public function testGet()
    {
        $this->markTestIncomplete('Not implemented yet.');
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

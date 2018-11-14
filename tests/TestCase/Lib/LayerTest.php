<?php
namespace App\Test\TestCase\Lib;

use App\Lib\Layer;
use Cake\TestSuite\TestCase;
use Cake\ORM\Locator\TableLocator;

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
    ];

    /**
     * Test subject
     *
     * @var \App\Form\LayerForm
     */
    public $Layer;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
//        print_r($this);
        parent::setUp();
        $table = $this->getTableLocator()->get('Pieces');
        $pieces = $table->find('all')->toArray();
        $this->Layer = new Layer($pieces);
        print_r($this->Layer->count());
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Layer);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

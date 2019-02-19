<?php
namespace App\Test\TestCase\Model\Lib;

use App\Model\Lib\LayerAccessArgs;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Lib\LayerAccessArgs Test Case
 */
class LayerAccessArgsTest extends TestCase
{
	public $args;

	public function setUp() {
		$this->args = new LayerAccessArgs();
	}
	
	public function tearDown() {
		unset($this->args);
	}

    /**
     * Test subject
     *
     * @var \App\Model\Lib\LayerAccessArgs
     */
    public $LayerAccessArgs;

    /**
     * Test __construct method
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test layer method
     *
     * @return void
     */
    public function testMultiSet()
    {
        $this->args->property('property')->limit('first');
		$this->assertTrue($this->args->valueOf('property') === 'property', 
				'The first setting of a chain did not persist');
		$this->assertTrue($this->args->valueOf('limit') === 1, 
				'The second setting of a chain did not persist');
    }

    /**
     * Test layer method
     *
     * @return void
     */
    public function testLayer()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test page method
     *
     * @return void
     */
    public function testPage()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test limit method
     *
     * @return void
     */
    public function testLimit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test property method
     *
     * @return void
     */
    public function testProperty()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test method method
     *
     * @return void
     */
    public function testMethod()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test conditions method
     *
     * @return void
     */
    public function testConditions()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test match method
     *
     * @return void
     */
    public function testMatch()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test valueOf method
     *
     * @return void
     */
    public function testValueOf()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

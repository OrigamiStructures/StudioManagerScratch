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
        $this->args->setLimit('first');
		$this->assertTrue($this->args->valueOf('limit') === 1,
				'Setting \limit\' to \'first\' did not result in the value 1');
		$this->args->setLimit('all');
		$this->assertTrue($this->args->valueOf('limit') === -1,
				'Setting \limit\' to \'all\' did not result in the value -1');
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
     * Test filter method
     *
     * @return void
     */
    public function testFilter()
    {
        $this->args->setLayer('piece')->specifyFilter('piece_id', [12,13,14,15]);
		$this->assertTrue($this->args->isFilter());
		$this->assertTrue($this->args->hasAccessNodeObject('value'));
		$this->assertTrue($this->args->valueOf('filterValue') === [12,13,14,15]);
		$this->assertTrue($this->args->valueOf('filterOperator') === 'in_array');
		
		$this->setUp();
		$this->args->specifyFilter('cityStateZip', '', '!=');
		$this->assertTrue($this->args->valueOf('filterOperator') === '!=');
		
    }

    /**
     * Test filterValue method
     *
     * @return void
     */
    public function testFilterValue()
    {
        $this->args->filterValue(FALSE);
		$this->assertTrue($this->args->valueOf('filter_value_isset'),
				"Setting the filter value to FALSE did not register "
				. "trigger filter_value_isset to become true.");
    }

    /**
     * Test filterOperator method
     *
     * @return void
     */
    public function testFilterOperator()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isFilter method
     *
     * @return void
     */
    public function testIsFilter()
    {
        $this->assertFalse($this->args->isFilter(),
				'unmodified argObject says it is a valid Filter');
		
		$this->args->setAccessNodeObject('value', '_value_source');
        $this->assertFalse($this->args->isFilter(),
				'argObject with only a _value_source says it is a valid Filter');
		
		$this->args->filterValue('filter_value');
        $this->assertTrue($this->args->isFilter(),
				'argObject with a _value_source and filter value says it is not a valid Filter');
						
    }

    /**
     * Test valueOf method
     *
     * @return void
     */
    public function testValueOf()
    {
        $this->args
				->setFilterOperator('==')
				->setLayer('layer');
		$this->assertTrue($this->args->valueOf('filter_operator') == '==', 
				"valueOf('filter_operator') did not return expected propery value");
		$this->assertTrue($this->args->valueOf('_filter_operator') == '==', 
				"valueOf('_filter_operator') did not return expected propery value");
		$this->assertTrue($this->args->valueOf('filterOperator') == '==', 
				"valueOf('filterOperator') did not return expected propery value");
		$this->assertTrue($this->args->valueOf('FilterOperator') == '==', 
				"valueOf('FilterOperator') did not return expected propery value");
		
		$this->assertTrue($this->args->valueOf('layer') == 'layer', 
				"valueOf('layer') did not return expected propery value");
		$this->assertTrue($this->args->valueOf('_layer') == 'layer', 
				"valueOf('layer') did not return expected propery value");
		$this->assertTrue($this->args->valueOf('Layer') == 'layer', 
				"valueOf('Layer') did not return expected propery value");
		$this->assertTrue($this->args->valueOf('bad_property') == '', 
				"valueOf('bad_property') did not return expected empty string");
		$this->assertTrue($this->args->valueOf(null) == '', 
				"valueOf(null) did not return expected empty string");
    }
}

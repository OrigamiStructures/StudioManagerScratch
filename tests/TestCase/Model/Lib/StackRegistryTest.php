<?php
namespace App\Test\TestCase\Model\Lib;

use App\Model\Lib\StackRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Lib\StackRegistry Test Case
 */
class StackRegistryTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Lib\StackRegistry
     */
    public $StackRegistry;
	
	public function setUp() {
		parent::setUp();
		$this->StackRegistry = new StackRegistry();
	}
	
	public function tearDown() {
		unset($this->StackRegistry);
		parent::tearDown();
	}

    /**
     * Test load method
     *
     * @return void
     */
    public function testLoad()
    {
		$name = 'sample';
		$result = $this->StackRegistry->load($name, 'data');

		$this->assertTrue($result == 'data', 'load()ing with both parameters '
				. 'did not return the provided data');
    }
	
	public function testLoadAsGet() {
		$name = 'sample';
		$this->StackRegistry->load($name, 'data');
		
        $this->assertTrue($this->StackRegistry->load($name) == 'data', 'load()ing '
				. 'with only one parameter did not return the provided data');
	}
	
	/**
	 * @expectedException \App\Exception\StackRegistryException
	 */
	public function testLoadUnknownValue() {
		$this->StackRegistry->load('uknown');
	}
	
	/**
	 * @expectedException \App\Exception\StackRegistryException
	 */
	public function testLoadSetValueTwice() {
		$name = 'sample';
		$this->StackRegistry->load($name, 'data');
		$this->StackRegistry->load($name, 'data');
	}

    /**
     * Test get method
     *
     * @return void
     */
    public function testGet()
    {
		$name = 'sample';
		$this->StackRegistry->load($name, 'data');
		
        $this->assertTrue($this->StackRegistry->get($name) == 'data', 'get()ing '
				. 'did not return the expected data');
    }

 	/**
	 * @expectedException \App\Exception\StackRegistryException
	 */
	public function testGetUnknownValue() {
		$this->StackRegistry->get('uknown');
	}

    /**
     * Test has method
     *
     * @return void
     */
    public function testHas()
    {
		$name = 'sample';
		$this->StackRegistry->load($name, 'data');
		
        $this->assertTrue($this->StackRegistry->has($name) == 'data',
				'has() didn\'t detect existing element');
    }

    /**
     * Test has method
     *
     * @return void
     */
    public function testHasNot()
    {
        $this->assertFalse($this->StackRegistry->has('unknown'));
    }
	
   /**
     * Test remove method
     *
     * @return void
     */
    public function testRemove()
    {
		$name = 'sample';
		$this->StackRegistry->load($name, 'data');
        $this->StackRegistry->remove($name);
		
        $this->assertFalse($this->StackRegistry->has($name));
    }

    /**
     * Test reset method
     *
     * @return void
     */
    public function testReset()
    {
		$name1 = 'sample';
		$name2 = 'test';
		$this->StackRegistry->load($name1, 'data');
		$this->StackRegistry->load($name2, 'data');
		
		$this->StackRegistry->reset();
        $this->assertFalse($this->StackRegistry->has($name1));
        $this->assertFalse($this->StackRegistry->has($name2));
    }
	
}

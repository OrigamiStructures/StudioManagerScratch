<?php
namespace App\Test\TestCase\Lib;

use App\Lib\RenumberRequests;
use App\Lib\RenumberRequest;
use Cake\TestSuite\TestCase;

/**
 * App\Lib\RenumberRequests Test Case
 */
class RenumberRequestsTest extends TestCase
{

	public $intNumberSet = [1,2,3,4,5];
	public $symNumberSet = ['A','B','C','D','E'];
	/**
     * Test subject
     *
     * @var \App\Lib\RenumberRequests
     */
    public $RenumberRequests;

    /**
     * Test __get method
     *
     * @return void
     */
//    public function testGet()
//    {
//        $this->markTestIncomplete('Not implemented yet.');
//    }

    /**
     * Test __construct method
     *
     * @return void
     */
    public function testConstruct()
    {
        $reqs = new RenumberRequests($this->intNumberSet);
		foreach ($reqs->validSymbols() as $key => $symbol) {
			$this->assertEquals($key, $symbol,
					"A key and value in the array of valid symbols did not match "
					. "when constructing from a set of numbers. key:$key, value:$symbol");
		}
		$this->assertInstanceOf('\SplHeap', $reqs->heap());
		
        $reqs = new RenumberRequests($this->symNumberSet);
		foreach ($reqs->validSymbols() as $key => $symbol) {
			$this->assertEquals($key, $symbol,
					"A key and value in the array of valid symbols did not match "
					. "when constructing from a set of letters. key:$key, value:$symbol");
		}
		$this->assertInstanceOf('\SplHeap', $reqs->heap());
    }

    /**
     * Test insert method
     *
     * @return void
     */
    public function testInsert()
    {
        $reqs = new RenumberRequests($this->intNumberSet);
        $reqs->insert(new RenumberRequest(2, 4));
		$this->assertArrayHasKey(2, $reqs->indexed());
//		$this->assertArrayHasKey(4, $reqs->indexed());
    }

    /**
     * Test validSymbols method
     *
     * @return void
     */
    public function testValidSymbols()
    {
        $reqs = new RenumberRequests($this->intNumberSet);
		$this->assertEquals($this->intNumberSet, array_keys($reqs->validSymbols()),
				'The keys of valid_symbol are wrong when construcing with an array of numbers');
		$this->assertEquals($this->intNumberSet, array_values($reqs->validSymbols()),
				'The values of valid_symbol are wrong when construcing with an array of numbers');
		
        $reqs = new RenumberRequests($this->symNumberSet);
		$this->assertEquals($this->symNumberSet, array_keys($reqs->validSymbols()),
				'The keys of valid_symbol are wrong when construcing with an array of letters');
		$this->assertEquals($this->symNumberSet, array_values($reqs->validSymbols()),
				'The values of valid_symbol are wrong when construcing with an array of letters');
    }

    /**
     * Test heap method
     *
     * @return void
     */
    public function testHeap()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test indexed method
     *
     * @return void
     */
    public function testIndexed()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test messagePackage method
     *
     * @return void
     */
    public function testMessagePackage()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

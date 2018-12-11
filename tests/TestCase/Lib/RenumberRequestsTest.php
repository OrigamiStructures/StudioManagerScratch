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
		foreach ($reqs->_valid_symbols as $key => $symbol) {
			$this->assertEquals($key, $symbol,
					"A key and value in the array of valid symbols did not match "
					. "when constructing from a set of numbers. key:$key, value:$symbol");
		}
		$this->assertInstanceOf('\SplHeap', $reqs->heap());
		
        $reqs = new RenumberRequests($this->symNumberSet);
		foreach ($reqs->_valid_symbols as $key => $symbol) {
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
		$this->assertArrayHasKey(2, $reqs->_indexed_list,
				'Inserting an RR object should make an _indexed_list entry '
				. 'with the RR->oldNum as the key');
		
		$xp = $reqs->_explicit_providers;
		$xr = $reqs->_explicit_receivers;
		foreach ($reqs->_indexed_list as $key => $request) {
			$nn = $request->newNum();
			$on = $request->oldNum();
			
			$this->assertArrayHasKey($key, $reqs->_valid_symbols,
					'Inserting RR objects should populate _indexed_list with '
					. 'instances of RenumberRequest');
			$this->assertInstanceOf('\App\Lib\RenumberRequest', $request,
					'After inserting RR objects, keys of _indexed_list should '
					. 'all be valid symbols');
			$this->assertArraySubset([$nn => [$on => $on]], $xp,
					'Inserting an object should make a newNum indexed explicit provider '
					. 'entry with an value indicating the oldNum receiver.');
			$this->assertArraySubset([$on => $on], $xr,
					'Inserting an object should make a oldNum indexed '
					. 'explicit receiver entry.');
		}
		
		$reqs->insert(new RenumberRequest(3,4));
		$xp = $reqs->_explicit_providers;
		$request = $reqs->_indexed_list[3];
		$nn = $request->newNum();
		$on = $request->oldNum();
		
		$this->assertArraySubset([$nn => [$on => $on]], $xp,
				'Inserting an object should make a newNum indexed explicit provider '
				. 'entry with an value indicating the oldNum receiver.');
		$this->assertEquals(2, count($xp[$nn]));
		$this->assertTrue((boolean) $request->duplicate_new_number);

		/*
		 * Test a leter symbol system
		 */
		$reqs = new RenumberRequests($this->symNumberSet);
        $reqs->insert(new RenumberRequest('C', 'B'));
		$this->assertArrayHasKey('C', $reqs->_indexed_list,
				'Inserting an RR object should make an _indexed_list entry '
				. 'with the RR->oldNum as the key');
		
		$xp = $reqs->_explicit_providers;
		$xr = $reqs->_explicit_receivers;
		foreach ($reqs->_indexed_list as $key => $request) {
			$nn = $request->newNum();
			$on = $request->oldNum();
			
			$this->assertArrayHasKey($key, $reqs->_valid_symbols,
					'Inserting RR objects should populate _indexed_list with '
					. 'instances of RenumberRequest');
			$this->assertInstanceOf('\App\Lib\RenumberRequest', $request,
					'After inserting RR objects, keys of _indexed_list should '
					. 'all be valid symbols');
			$this->assertArraySubset([$nn => [$on => $on]], $xp,
					'Inserting an object should make a newNum indexed explicit provider '
					. 'entry with an value indicating the oldNum receiver.');
			$this->assertArraySubset([$on => $on], $xr,
					'Inserting an object should make a oldNum indexed '
					. 'explicit receiver entry.');
		}
		
		$reqs->insert(new RenumberRequest('A', 'B'));
		$xp = $reqs->_explicit_providers;
		$request = $reqs->_indexed_list['A'];
		$nn = $request->newNum();
		$on = $request->oldNum();
		
		$this->assertArraySubset([$nn => [$on => $on]], $xp,
				'Inserting an object should make a newNum indexed explicit provider '
				. 'entry with an value indicating the oldNum receiver.');
		$this->assertEquals(2, count($xp[$nn]));
		$this->assertTrue((boolean) $request->duplicate_new_number);
	}

    /**
     * Test validSymbols method
     *
     * @return void
     */
    public function testValidSymbols()
    {
        $reqs = new RenumberRequests($this->intNumberSet);
		$this->assertEquals($this->intNumberSet, array_keys($reqs->_valid_symbols),
				'The keys of valid_symbol are wrong when construcing with an array of numbers');
		$this->assertEquals($this->intNumberSet, array_values($reqs->_valid_symbols),
				'The values of valid_symbol are wrong when construcing with an array of numbers');
		
        $reqs = new RenumberRequests($this->symNumberSet);
		$this->assertEquals($this->symNumberSet, array_keys($reqs->_valid_symbols),
				'The keys of valid_symbol are wrong when construcing with an array of letters');
		$this->assertEquals($this->symNumberSet, array_values($reqs->_valid_symbols),
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
		$reqs = new RenumberRequests($this->symNumberSet);
		$data = ['A'=>'B','B'=>'C','C'=>'D','D'=>'A','E'=>'A'];
		foreach($data as $on => $nn) {
			$reqs->insert(new RenumberRequest($on, $nn));
		}
        $indexedStorage = $reqs->indexed();
		$this->assertEquals(5, count($indexedStorage));
		foreach ($indexedStorage as $on => $request) {
			$this->assertInstanceOf('\App\Lib\RenumberRequest', $request,
					'After inserting RR objects, keys of _indexed_list should '
					. 'all be valid symbols');
			$this->assertEquals($on, $request->oldNum());
		}
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

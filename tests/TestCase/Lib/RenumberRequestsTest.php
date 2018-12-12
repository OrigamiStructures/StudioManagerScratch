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
			$this->assertTrue($reqs->heap()->count() > 0, 'Inserting an RR object should '
					. 'add an element to the heap ');
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
		$this->assertTrue((boolean) $request->_duplicate_new_number);

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
			$this->assertTrue($reqs->heap()->count() > 0, 'Inserting an RR object should '
					. 'add an element to the heap ');
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
		$this->assertTrue((boolean) $request->_duplicate_new_number);
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
        $reqs = new RenumberRequests($this->intNumberSet);
        $reqs->insert(new RenumberRequest(2, 4));
		$heap = $reqs->heap();
		$this->assertInstanceOf('\SplHeap', $reqs->_heap, 
				'The private property _heap should be an SplHeap object');
		$this->assertInstanceOf('\SplHeap', $reqs->heap(), 
				'The returned object should be an SplHeap object');
		$this->assertTrue($reqs->_heap == $reqs->heap(),
				'The heap property and returned heap should have the same content. The '
				. 'return should be a clone');
		$this->assertFalse($reqs->_heap === $reqs->heap(),
				'The heap property and returned heap should not be the same object. The '
				. 'return should be a clone');
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
        $reqs = new RenumberRequests($this->intNumberSet);
        $reqs->insert(new RenumberRequest(2, 4));
		$messages = $reqs->messagePackage();
		$this->assertInstanceOf('\App\Lib\RenumberMessaging', $messages, 
				'The returned object should be an RenumberMessaging object');
    }
	
	public function testDuplicateDetection() {
		$reqs = new RenumberRequests($this->symNumberSet);
		$data = ['A'=>'B','B'=>'C','C'=>'B','D'=>'A','E'=>'B'];
		foreach($data as $on => $nn) {
			$reqs->insert(new RenumberRequest($on, $nn));
		}
		$messages = $reqs->messagePackage();
		foreach (['A','C','E'] as $oldNum) {
			$this->assertTrue((boolean) $reqs->request($oldNum)->_duplicate_new_number,
					'Recieving a duplicate use of a new number should set the '
					. 'duplicate new number flag in the request');
			$this->assertArraySubset([$oldNum => ["Can't change multiple pieces (3) to #B."]], $messages->errors(),
					'Recieving a duplicate use of a new number should place an error '
					. 'message in the messagePackage');
		}
		foreach (['B','D'] as $oldNum) {
			$this->assertFalse($reqs->request($oldNum)->_duplicate_new_number,
					'A valid request should not set the '
					. 'duplicate new number flag in the request');
		}
	}
	
	public function testImpliedRequestCreationDetection() {
		$reqs = new RenumberRequests($this->symNumberSet);
		$reqs->insert(new RenumberRequest('A', 'B'));
		$reqs->insert(new RenumberRequest('C', 'D'));
		$reqs->insert(new RenumberRequest('D', 'C'));
		$messages = $reqs->messagePackage();
		$this->assertTrue($reqs->request('B')->_implied_change,
					'When one receiver and one provider are left after checklist-process '
				. 'an implied change request should be constructed');
		$this->assertArraySubset(['B' => ["Other changes implied the change of "
					. "#B to #A."]], $messages->errors(),
				'When one receiver and one provider are left after checklist-process '
				. 'an implied change message should be written');
		$this->assertFalse($reqs->request('D')->_implied_change,
					'When a valid request is made '
				. 'an implied change request should not be constructed');
	}
	
	/**
	 * Test vague provider rule detection
	 * 
	 * Vauge provider is a piece that gets a new number but 
	 * it can't be determined where to pass the old number
	 */
	public function testVaugeProviderDetection() {
		$reqs = new RenumberRequests($this->symNumberSet);
		$data = ['A'=>'B','C'=>'D'];
		foreach($data as $on => $nn) {
			$reqs->insert(new RenumberRequest($on, $nn));
		}
		$messages = $reqs->messagePackage();
		foreach (['B','D'] as $oldNum) {
			$this->assertTrue($reqs->request($oldNum)->_vague_provider,
					'When a piece provides a new number but doesn\'t act as a receiver '
					. 'it should be flagged for its vauge provider');
			$this->assertArraySubset([$oldNum => ["#$oldNum was reassigned but no new number was provided."]], $messages->errors(),
					'When a piece provides a new number but doesn\'t act as a receiver '
					. 'it should be generate a vauge provider message');
		}
		foreach (['A','C'] as $oldNum) {
			$this->assertFalse($reqs->request($oldNum)->_vague_provider,
					'A valid request should not set the '
					. 'vague provider flag in the request');
		}
	}
	
	
	/**
	 * Test vague receiver rule detection
	 * 
	 * Vauge provider is a piece that gets a new number but 
	 * it can't be determined where to pass the old number
	 */
	public function testMissingRecieverDetection() {
		$reqs = new RenumberRequests($this->symNumberSet);
		$data = ['A'=>'B','C'=>'D'];
		foreach($data as $on => $nn) {
			$reqs->insert(new RenumberRequest($on, $nn));
		}
		$messages = $reqs->messagePackage();
		foreach (['A','C'] as $oldNum) {
			$this->assertTrue($reqs->request($oldNum)->_vague_receiver,
					'When a piece receives a new number but doesn\'t act as a receiver '
					. 'it should be flagged as a vauge receiver');
			$this->assertArraySubset([$oldNum => ["Can't determine which piece should receive #$oldNum."]], $messages->errors(),
					'When a piece receives a new number but doesn\'t act as a receiver '
					. 'it should be generate a vauge receiver message');
		}
		foreach (['B','D'] as $oldNum) {
			$this->assertFalse($reqs->request($oldNum)->_vague_receiver,
					'A valid request should not set the '
					. 'vague receiver flag in the request');
		}
		
	}

}

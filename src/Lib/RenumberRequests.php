<?php
namespace App\Lib;

use SplHeap;
use App\Lib\RenumberRequest;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\Model\Table\PiecesTable;
use App\Lib\SystemState;
use Cake\Utility\Text;
use App\Lib\RenumberMessaging;
use Cake\Core\ConventionsTrait;
use Cake\Utility\Hash;

/**
 * RenumberRequests
 * 
 * This class sorts and validates piece number change requests. 
 * It keeps the requests in a heap and builds messages to explain 
 * when vague or impossible combinations of moves are requested. 
 * 
 * The heap actually contains RenumberRequest objects, one for each request.
 *
 * @author dondrake
 */

class RenumberRequests {
	
	use ConventionsTrait;
	
	/**
	 * The ordered list of change requests, deductions, and error notices
	 * 
	 * The heap contains RenumberRequest objects that contain their 
	 * old number, new number request value, and info about the 
	 * validity and origin of the request. Each request has a message() 
	 * method to report to the user.
	 * 
	 * The sorting order is ASCENDING on the sum of newNum + oldNum
	 *
	 * @var iterator
	 */
	private $_heap;
	
	/**
	 * Reference copies of all the involved RenumberRequest objects
	 * 
	 * These are the same objects contained in the heap but they are 
	 * indexed by $request->old for easy look-up
	 * 
	 * <code>
	 * _index_list [
	 *		oldNum => RenumberRequest{ },
	 *		oldNum => RenumberRequest{ },
	 * ]
	 * </code>
	 *
	 * @var array
	 */
	private $_indexed_list;
	
	/**
	 * The full set of possible symbols or number of pieces
	 * 
	 * <code>
	 * _valid_symbols [
	 *		num => num,
	 *		num => num,
	 * ]
	 * </code>
	 *
	 * @var array
	 */
	protected $_valid_symbols;
	
	/**
	 * New numbers that were used buth which are not _valid_symbols
	 * 
	 * <code>
	 * $_bad_symbols [
	 *		newNum => newNum,
	 *		newNum => newNum,
	 * ]
	 * </code>
	 * 
	 * @var array
	 */
	private $_bad_symbols;

	/**
	 * List of piece numbers that are being assigned to new owners
	 * 
	 * Built from request->new
	 * The implication is that these pieces will need to receive new numbers
	 * 
	 * <code>
	 * $_explicit_providers[
	 *	newNum => [
	 *		oldNum => oldNum,
	 *		oldNum => oldNum. 
	 *	], 
	 *	newNum => [repeat],
	 * ]
	 * </code>
	 *
	 * @var array
	 */
	private $_explicit_providers = [];

	/**
	 * To verify that all providers are also receiving
	 * 
	 * <code>
	 *  $this_receiver_checklist = $this->providers_mentioned
	 * </code>
	 * 
	 * Built from the number keys of $_explicit_providers. The 
	 * _explicit_receivers are ok by definition. But the providers 
	 * were only mentioned and we'll have to use this list to 
	 * confirm that each one is actually receiving a number to 
	 * replace the the one it provided.
	 *
	 * @var array
	 */
	private $_reciever_checklist = FALSE;
	
	/**
	 * List of pieces explicitly receiving new numbers
	 * 
	 * Built from request->old
	 * The implication is that these pieces will need to provide 
	 * their numbers to other pieces
	 * 
	 * <code>
	 * $explicit_receivers[ 
	 *		oldNum-x => oldNum-x, 
	 *		oldNum-y => oldNum-y
	 * ]
	 * </code>
	 *
	 * @var array
	 */
	private $_explicit_receivers;

	/**
	 * To verify that all receivers mentioned are also providing
	 *
	 * <code>
	 *  _provider_checklist = _explicit_receivers = [
	 *		oldNum => oldNum,
	 *		oldNum => oldNum,
	 *  ]
	 * </code>
	 * 
	 * This is a duplicate of $_explicit_receivers. We'll have 
	 * to go through each one to verify that they are also  
	 * providing their displaced numbers.
	 * 
	 * @var array
	 */
	private $_provider_checklist = FALSE;
	
	/**
	 * Give debugging access to internal properties
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {
		if (Configure::read('debug')) {
			return $this->$name;
		}
	}
	
	/**
	 * If new entries go in the heap, any internal calculated values must update
	 * 
	 * This will tell us if the internal logic must 
	 * run or if we can just use the analyzed properties as they stand
	 *
	 * @param array $valid_numbers Values are the full set of valid numbers
	 */
	public function __construct($valid_numbers) {
		$this->_valid_symbols = array_combine($valid_numbers, $valid_numbers);
		$this->_heap = new RenumberRequestHeap();
	}
	
	
	public function validSymbols() {
		return $this->_valid_symbols;
	}
	public function providers() {
		return $this->_explicit_providers;
	}
	public function receivers() {
		return $this->_receivers_mentioned;
	}
	
	/**
	 * 
	 * 
	 * @param RenumberRequest $request
	 * @return RenumberRequest 
	 */
	public function insert($request) {
		$this->badSymbolCheck($request);
		$this->_heap->insert($request);
		$this->storeRequest($request);
		$this->updateProviders($request, 'addTarget');
		$this->recordReceiverMention($request);
	}
	
	/**
	 * Record bad-symbol use if it is detected
	 * 
	 * @param RenumberRequest $request
	 * @return void
	 */
	protected function badSymbolCheck($request) {
		if (!in_array($request->newNum(), $this->_valid_symbols)) {
			$request->badNumber(TRUE);
			$this->_bad_symbols[$request->newNum()] = $request->newNum();
		}
	}
	
	/**
	 * Store a reference to the object indexed by oldNum
	 * 
	 * @param RenumberRequest $request
	 */
	protected function storeRequest($request) {
		$this->_indexed_list[$request->oldNum()] = $request;
	}

	/**
	 * Return the current heap iterator
	 * 
	 * The heap will contain RenumberRequest objects 
	 * in a convenient sort order
	 * 
	 * New numbers that are invalid and duplicate number errors 
	 * have been encoded in the objects during insertion. But we 
	 * need to make a sweep through to generate any moves that 
	 * are implied but not explicitly requested.
	 * 
	 * @return iterator
	 */
	public function heap(){
		if ($this->_heap->count() > 0 && $this->_provider_checklist === FALSE) {
			$this->_completness_scan();
		}
		return clone($this->_heap);
	}
	
	/**
	 * Return the request objects indexed by old number
	 * 
	 * Contains references to the same RenumberRequest objects 
	 * as the heap, but indexed for easy access. 
	 * 
	 * @return array
	 */
	public function indexed() {
		return $this->_indexed_list;
	}
	
	public function messagePackage() {
		$requests = [];
		foreach ($this->heap() as $request) {
			$requests[$request->old] = $request;
		}
		return new RenumberMessaging($requests);
	}
	
	/**
	 * Return the RenumberRequest object with $oldNum
	 * 
	 * @param int $oldNum
	 * @return RenumberRequest
	 */
	public function request($oldNum) {
		if (isset($this->_indexed_list[$oldNum])) {
			return $this->_indexed_list[$oldNum];
		}
		return FALSE;
	}
	
	/**
	 * Get the numbers being changed to this provider's number
	 * 
	 * There should only be one. Anything else is tracked as an error. 
	 * 
	 * @param int $newNum
	 * @return array
	 */
	protected function providerTargets($newNum) {
		return $this->_explicit_providers[$newNum];
	}
	
	/**
	 * Increment or decrement and entry in the 'from' usage tables
	 * 
	 * the 'to' table became more complicated and was removed
	 * 
	 * @param int $index
	 * @param string $silo
	 * @param int $delta
	 */
	private function _record_use($index, $silo, $delta) {
		if (!isset($this->{$silo}[$index])) {
			$this->{$silo}[$index] = $delta;
		} else {
			$this->{$silo}[$index] += $delta;
		}
	}
	
	protected function recordReceiverMention(RenumberRequest $request) {
		$this->_explicit_receivers[$request->old] = $request->old;
//		$this->_record_use(, '_explicit_receivers', 1);
	}

	/**
	 * Discover how many times this provider's number is used
	 * 
	 * Anything other than 1 is an error condition
	 * 
	 * @param RenumberRequest $request
	 * @return int
	 */
	protected function providerUseCount($request) {
		$newNum = $request->newNum();
		return count($this->_explicit_providers[$newNum]);
//		return $this->_explicit_providers[$request->newNum()]['count'];
	}
	
	/**
	 * Make a change to the _explicit_providers property
	 * 
	 * @param RenumberRequest $request
	 * @param string $mode What kind of property change to make
	 */
	protected function updateProviders($request, $mode = 'addTarget') {
		$newNum = $request->newNum();
		$oldNum = $request->newNum();
		if (!Hash::check($this->_explicit_providers, "$newNum")) {
			$this->_explicit_providers[$newNum] = [];
		}
		switch ($mode) {
			case 'addTarget':
				$this->_explicit_providers[$newNum][$oldNum] = $oldNum;
				$target = $this->request($oldNum);
				if ($target) { // how would this not be true?
					$target->duplicate($this->providerUseCount($request));
				}
				break;
			case 'dropTarget':
				// unused. assumed to be for ajax processes later
				unset($this->_explicit_providers[$newNum][$oldNum]);
				break;
			default:
				break;
		}
	}
	
	/**
	 * Before releasing the heap, we do a final analysis of the data
	 * 
	 * This takes all the requests that were sent in and compares all the 
	 * pieces receiving/providing numbers and makes sure they 
	 * provide/receive in a reciprocal way. 
	 * 
	 * This will reveal any implied move we can guess and any implied 
	 * moves that can't be guessed. We'll add messaging for these too.
	 */
	protected function _completness_scan() {
		$this->_provider_checklist = $this->_explicit_receivers;
		$this->_reciever_checklist = $this->_explicit_providers;
		foreach($this->_indexed_list as $request) {
			if ($request->_bad_new_number) {
				// bad providers can be disregarded. Already have error message
				unset($this->_reciever_checklist[$request->new]);
				unset($this->_provider_checklist[$request->old]);
			} else {
				unset($this->_reciever_checklist[$request->old]);
				unset($this->_provider_checklist[$request->new]);
			}
		}
		$_providers = count($this->_provider_checklist);
		$_receivers = count($this->_reciever_checklist);
		
		if ($_providers === 1 && $_receivers === 1) {
			$request = $this->_create_implied_request();
			$this->insert($request);
			unset($this->_reciever_checklist[$request->old]);
			unset($this->_provider_checklist[$request->new]);
		}
		if (count($this->_provider_checklist) > 0) {
			foreach ($this->_provider_checklist as $number){
				$this->_indexed_list[$number]->vagueReceiver(TRUE);
			}
		}
		if (count($this->_reciever_checklist) > 0) {
			// number transfered but no replacement number provided
			foreach ($this->_reciever_checklist as $number => $use) {
				$request = (new RenumberRequest($number, NULL));
				$this->insert($request);
			}
		}
		return;
	}
	
	/**
	 * 
	 * 
	 * $discovered_new should be the same value as the single 
	 * entry remaining in _receiver_checklist. Worth checking? 
	 * I can't think of an edge case that would require the check. 
	 * 
	 * @return RenumberRequest
	 */
	private function _create_implied_request() {
			$discovered_old = array_keys($this->_reciever_checklist)[0];
			$discovered_new = array_keys($this->_provider_checklist)[0];
			return (new RenumberRequest($discovered_old, $discovered_new))->implied(TRUE);
	}
	
	public function __debugInfo() {
		$properties = get_class_vars(get_class($this));
		$output = [];
		foreach ($properties as $name => $value) {
			$output[$name] = $this->$name;
		}
		return $output;
	}
}

/**
 * The heap implementation to store sorted RenumberRequest objects
 * 
 * The objects are stored in ascending order on the sum of 
 * their oldNum + newNum
 */
class RenumberRequestHeap extends SplHeap {
	
    /**
     * Sorting is based on the sum of the old and new number
	 * Lowest values are first in the list 
     */
    protected function compare($request1, $request2)
    {
        $r1 = $request1->newNum() + $request1->oldNum();
        $r2 = $request2->newNum() + $request2->oldnum();
        if ($r1 === $r2){
			return 0;
		}
        return $r1 > $r2 ? -1 : 1;
    }
	
}


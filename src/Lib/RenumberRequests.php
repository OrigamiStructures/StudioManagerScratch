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
	
	/**
	 * The ordered list of change requests, deductions, and error notices
	 * 
	 * The heap contains RenumberRequest objects that contain their 
	 * old number, new number request value, and info about the 
	 * validity and origin of the request. Each request has a message() 
	 * method to report to the user.
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
	 * @var array
	 */
	private $_indexed_list;
	
	private $_valid_symbols;
	
	private $_bad_symbols;

	/**
	 * List of piece numbers that are being assigned to new owners
	 * 
	 * Built from request->new
	 * The implication is that these pieces will need to receive new numbers
	 * 
	 * $_explicit_receivers[number][count][target]
	 * where 'number' is the symbol that is being assigned as a number
	 * where 'count' contains an integer indicating how many pieces 
	 *		will receive the number used as an error indicator since 
	 *		the only correct answer is '1' 
	 * where 'target' contains an array of the old pieces numbers for the 
	 *		pieces that will receive this new number (in the form [x => x, y => y]
	 *		'target' is included for debugging and is not used in logic. 
	 *		But it could be used to group messages during _completeness_scan()
	 *
	 * @var array
	 */
	private $_providers_mentioned;

	/**
	 * To verify that all providers are also receiving
	 * 
	 * [ x => x, y => y ]
	 * Built from the number keys of $_providers_mentioned. The 
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
	 * $_providers_mentioned[ x => x, y => y]
	 *
	 * @var array
	 */
	private $_explicit_receivers;
	

	/**
	 * To verify that all receivers mentioned are also providing
	 *
	 * [ x => x, y => y ]
	 * This is a duplicate of $_explicit_receivers. We'll have 
	 * to go through each one to verify that they are also  
	 * providing their displaced numbers.
	 * 
	 * @var array
	 */
	private $_provider_checklist = FALSE;
	
	private $_message = [];
		
	/**
	 * This would allow numbers that were not in the original list
	 * 
	 * This is a proposed feature. I guess the idea would be to just 
	 * change the old number into the new number without the normal 'move' 
	 * of the new number from another piece. 
	 * 
	 * This implies different kinds of error checking and rule 
	 * implementation. Possibly a different class would be the best 
	 * way to activate this new strategy.
	 *
	 * @var boolean
	 */
	private $_loose = FALSE;
	
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
		return NULL;
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
	
	/**
	 * 
	 * @param RenumberRequest $request
	 * @return RenumberRequest 
	 */
	public function insert(RenumberRequest $request) {
		if (!$this->_loose && !array_key_exists($request->new, $this->_valid_symbols)) {
			$request->badNumber(TRUE);
			$this->_bad_symbols[$request->new] = $request->new;
		}
		$this->_heap->insert($request);
		$this->_indexed_list[$request->old] = $request;
		
		$this->_inc_providers($request);
		$this->_inc_receivers($request);
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
		if ($this->_provider_checklist === FALSE) {
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
	 * Track errors related to multiple assignment of a new number
	 * 
	 * Find the cases where a new number is assigned multiple times and 
	 * mark the pieces targeted for the new number. The number of uses 
	 * will be recorded in the target for message purposes.
	 * 
	 * @todo This could be eliminated if done automatically on ->insert($request)
	 */
	private function _markDuplicateUse($reqest) {
		foreach ($this->_providers_mentioned[$reqest->new]['target'] as $target) {
			$this->_indexed_list[$target]->duplicate(
					$this->_providers_mentioned[$reqest->new]['count']);
		}
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
	
	protected function _inc_receivers(RenumberRequest $request) {
		$this->_explicit_receivers[$request->old] = $request->old;
//		$this->_record_use(, '_explicit_receivers', 1);
	}

	/**
	 * It's assumed that this will support ajax in the future
	 * 
	 * For now, there is no conceivable calling situation
	 * 
	 * @param RenumberRequest $request
	 */
	protected function _dec_receivers(RenumberRequest $request) {
		$this->_record_use($request->old, '_explicit_receivers', -1);
	}

	protected function _inc_providers(RenumberRequest $request) {
		if (!isset($this->_providers_mentioned[$request->new])) {
			$this->_providers_mentioned[$request->new]['count'] = 1;
			$this->_providers_mentioned[$request->new]['target'] = NULL;
		} else {
			$this->_providers_mentioned[$request->new]['count']++;
		}
		$this->_providers_mentioned[$request->new]['target'][$request->old] = $request->old;
		if ($this->_providers_mentioned[$request->new]['count'] > 1) {
			$this->_markDuplicateUse($request);
		}		
	}

	/**
	 * It's assumed that this will support ajax in the future
	 * 
	 * For now, there is no conceivable calling situation
	 * 
	 * @param RenumberRequest $request
	 */
	protected function _dec_providers(RenumberRequest $request) {
		$this->_providers_mentioned[$request->new]['count']--;
		unset($this->_providers_mentioned[$request->new]['target'][$request->old]);
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
		$this->_reciever_checklist = $this->_providers_mentioned;
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
	
}

class RenumberRequestHeap extends SplHeap {
	
    /**
     * Sorting is based on the sum of the old and new number
	 * Lowest values are first in the list 
     */
    protected function compare($request1, $request2)
    {
        $r1 = $request1->new + $request1->old;
        $r2 = $request2->new + $request2->old;
        if ($r1 === $r2){
			return 0;
		}
        return $r1 > $r2 ? -1 : 1;
    }
	
}


<?php
namespace App\Model\Lib;

use App\Model\Lib\StackSet;

/**
 * Description of ManagementManifestSet
 *
 * @author dondrake
 */
class ManagerManifestStacksSet extends StackSet{
	
	public function ownedManagement($supervisor_id) {
		$owned = $this
			->find('manifest')
			->specifyFilter('supervisor_id', $supervisor_id)
			->loadStacks();
		return $owned;
	}
	
	public function delegatedManagement($supervisor_id) {
		$collection = collection($this->getData());
		$delegated = $collection->filter(function($stack) use ($supervisor_id) {
			return $stack->rootElement()->supervisorId() == $supervisor_id
					&& !$stack->rootElement()->selfAssigned();
		});
		return $delegated->toArray();
	}
	
	public function receivedManagement($supervisor_id) {
		$received = $this
			->find('manifest')
			->specifyFilter('supervisor_id', $supervisor_id, '!=')
			->loadStacks();
		return $received;
	}
	
//	public function ownedAritists() {
//		return [];
//	}
//	
//	public function delegatedArtists() {
//		return [];
//	}
//	
//	public function recievedArtists() {
//		return [];
//	}
	
}

<?php
namespace App\Model\Lib;

use App\Model\Lib\StackSet;

/**
 * ManifestSet
 *
 * A wrapper class to add features to StackSets of ManifestStacks
 *
 * @author dondrake
 */
class ManifestStacksSet {

    public function __construct($stackSet)
    {
        $this->stackSet = $stackSet;
    }

    public function __call($name, $arguments)
    {
        return $this->stackSet->$name($arguments);
    }

    public function ownedManagement($supervisor_id)
    {
		$owned = $this
			->find('manifest')
			->specifyFilter('supervisor_id', $supervisor_id)
			->loadStacks();
		return $owned;
	}

	public function delegatedManagement($supervisor_id)
    {
		$collection = collection($this->stackSet->getData());
		$delegated = $collection->filter(function($stack) use ($supervisor_id) {
			return $stack->rootElement()->supervisorId() == $supervisor_id
					&& !$stack->rootElement()->selfAssigned();
		});
		return $delegated->toArray();
	}

	public function receivedManagement($supervisor_id)
    {
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

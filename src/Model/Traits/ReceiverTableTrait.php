<?php
namespace App\Model\Traits;

/**
 * Description of ReceiverTrait
 *
 * @author dondrake
 */
trait ReceiverTableTrait {
	
	public function initializeReceiverCard() {
	    $this->addLayerTable(['Dispositions']);
		$this->stackSchema[] = ['name' => 'dispositions',	'specs' => ['type' => 'layer']];
		$this->seedPoints = array_merge($this->seedPoints, [
			'disposition', 
			'dispositions'
			]);
	}
	
	public function distillFromDisposition($ids) {
		$IDs = $this->Dispositions->find('list', ['valueField' => 'member_id'])
				->where(['id IN' => $ids])
				->toArray();
		return array_unique($IDs);
	}
	
	public function marshalDispositions($id, $stack) {
		if ($stack->count('identity')) {
			$dispositions = $this->Dispositions->find('all')
					->where(['member_id' => $id]);
			$stack->set(['dispositions' => $dispositions->toArray()]);
		}
		return $stack;
	}
	
	
}

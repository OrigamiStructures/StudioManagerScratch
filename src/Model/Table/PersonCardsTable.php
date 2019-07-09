<?php
namespace App\Model\Table;

use App\Model\Table\RolodexCardsTable;

use Cake\ORM\Table;
use App\Model\Traits\ContactableTableTrait;
use App\Model\Traits\ReceiverTableTrait;

/**
 * CakePHP PersonCardsTable
 * @author dondrake
 */
class PersonCardsTable extends RolodexCardsTable {
	
	
	use ContactableTableTrait;
	use ReceiverTableTrait;
	
	public function initialize(array $config) {
		parent::initialize($config);
		$this->initializeContactableCard();
		$this->initializeReceiverCard();
		$this->addLayerTable(['Images']);
        $this->addStackSchema(['image']);
		$this->addSeedPoint(['image', 'images']);
	}
	
	protected function distillFromImage($ids) {
		$IDs = $this->Identities->find('list', ['valueField' => 'id'])
				->where(['image_id IN' => $ids]);
		return $this->distillFromIdentity($IDs);
	}
	
	protected function marshalImage($id, $stack) {
//		debug($stack);
		if ($stack->count('identity')) {
			$image = $this->Images->find('all')
					->where(['id' => $stack->rootElement()->imageId()]);
			$stack->set(['image' => $image->toArray()]);
		}		
		return $stack;
	}

	protected function marshalIdentity($id, $stack) {
			$identity = $this->Identities
                ->find('all')
                ->where(['id' => $id, 'member_type' => 'Person']);
			$stack->set(['identity' => $identity->toArray()]);
			return $stack;
	}
	
	protected function localConditions($query, $options = []) {
		return $query->where(['member_type' => 'Person']);
	}
	
}

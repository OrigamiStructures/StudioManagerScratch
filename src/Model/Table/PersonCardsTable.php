<?php
namespace App\Model\Table;

use App\Model\Table\RolodexCardsTable;

use Cake\ORM\Table;
use App\Model\Traits\ContactableTableTrait;
use App\Model\Traits\ReceiverTableTrait;
use App\Model\Lib\PersonCardRegistry;

/**
 * PersonCardsTable
 * 
 * Create PersCards, a variety of stack entity. 
 * Enforce that only member records of member_type === Person are included. 
 * All possible distillers for this class
 *   identity (root)
 *   data_owner
 *   manager
 *   membership
 *   supervisor
 *   image
 *   addresses
 *   contact
 * All possible distillers for this classdisposition
 * 
 * 
 * @author dondrake
 */
class PersonCardsTable extends RolodexCardsTable {
	
	use ContactableTableTrait;
	use ReceiverTableTrait;
	
	protected $registry;
	
	public function initialize(array $config) {
		parent::initialize($config);
		$this->initializeContactableCard();
		$this->initializeReceiverCard();
		$this->addLayerTable(['Images']);
        $this->addStackSchema(['image']);
		$this->addSeedPoint(['image', 'images']);
		$this->registry = new PersonCardRegistry();
	}
	
	protected function distillFromImage($ids) {
		$query = $this->Identities->find('list', ['valueField' => 'id'])
				->where(['image_id IN' => $ids]);
		return $this->distillFromIdentity($query->toArray());
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
                ->where(['id' => $id]);
			$stack->set(['identity' => $identity->toArray()]);
			return $stack;
	}
	
	protected function localConditions($query, $options = []) {
		return $query->where(['member_type' => 'Person']);
	}
	
}

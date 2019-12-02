<?php
namespace App\Model\Table;

use App\Model\Table\RolodexCardsTable;

use Cake\ORM\Table;
use App\Model\Traits\ContactableTableTrait;
use App\Model\Traits\ReceiverTableTrait;
//use App\Model\Lib\PersonCardRegistry;

/**
 * PersonCardsTable
 *
 * Create PersonCards, a variety of stack entity.
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
 * All possible distillers for this class disposition
 *
 *
 * @author dondrake
 * @property ManifestsTable $Manifests
 * @property IdentitiesTable $Identities
 * @property ImagesTable $Images
 */
class PersonCardsTable extends RolodexCardsTable {

	use ContactableTableTrait;
	use ReceiverTableTrait;

	public function initialize(array $config) {
		parent::initialize($config);
		$this->initializeContactableCard();
		$this->initializeReceiverCard();
		$this->addLayerTable(['Images', 'Manifests']);
        $this->addStackSchema(['image', 'manifests']);
		$this->addSeedPoint(['image', 'images', 'manifest', 'manifests']);
//		$this->registry = new PersonCardRegistry();
	}

	protected function distillFromImage($ids) {
		$query = $this->Identities->find('list', ['valueField' => 'id'])
				->where(['image_id IN' => $ids]);
		return $this->distillFromIdentity($query->toArray());
	}

    /**
     * @param array $ids
     * @return array
     */
    protected function distillFromManifest(array $ids)
    {
        $query = $this->Manifests->find('all')
            ->where(['id IN' => $ids]);
        $manifests = new Collection($query->toArray());
        $result = $manifests->reduce(function ($accum, $entity) {
                $accum['userId'][]=$entity->supervisorId();
                $accum['userId'][]=$entity->managerId();
                $accum['memberId'][]=$entity->artistId();
                return $accum;
            }, ['userId' => [], 'memberId' => []]);
        return array_unique($result);
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

    /**
     * @param $id
     * @param $stack StackEntity
     * @return StackEntity
     */
    protected function marshalManifests($id, $stack)
    {
        $person_id = $stack->rootID();
        $manifest = $this->Manifests
            ->find('all')
            ->where([
                'OR' => [
                    'supervisor_id' => $person_id,
                    'manager_id' => $person_id,
                    'member_id' => $person_id
                ]
            ]);
        $stack->set(['manifests' => $manifest->toArray()]);
        return $stack;
    }

}

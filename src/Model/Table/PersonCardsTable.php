<?php
namespace App\Model\Table;

use App\Model\Entity\Manifest;
use App\Model\Entity\PersonCard;
use App\Model\Lib\StackSet;
use App\Model\Table\RolodexCardsTable;

use Cake\ORM\Table;
use App\Model\Traits\ContactableTableTrait;
use App\Model\Traits\ReceiverTableTrait;
use Cake\Collection\Collection;
use Cake\ORM\TableRegistry;

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
     * Locate the Member IDs implicated in a set of Manifests
     *
     * @param array $ids
     * @return Query
     */
    protected function distillFromManifest(array $ids)
    {
        $contextUser = $this->contextUser();
        $query = $this->Manifests->find('all')
            ->where(['id IN' => $ids]);
        $manifests = new Collection($query->toArray());
        $result = $manifests->reduce(function ($accum, $entity) use ($contextUser) {
            /* @var Manifest $entity */
            /**
             * Here we should filter to insure the user has access
             * to the person cards we derive.
             * If the User is not a superuser, the the userID must
             * match either SupervisorId or ManagerId. When it does
             * match, then all the person cards are allowed.
             */
            if (($contextUser->isSuperuser() && is_null($contextUser->getId('supervisor')))
                || $contextUser->getId('supervisor') == $entity->getOwnerId('supervisor')
                || $contextUser->getId('supervisor') == $entity->getOwnerId('manager')
            ) {
                $accum[] = $entity->getSupervisorMember();
                $accum[] = $entity->getManagerMember();
                $accum[] = $entity->artistId();
            }
            return $accum;
            }, []);
        return $this->distillFromIdentity(array_unique($result));
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
     * @param $stack PersonCard
     * @return PersonCard
     */
    protected function marshalManifests($id, $stack)
    {
        $person_id = $stack->rootID();
        /**
         * We can't return every manifest for the person. If this
         * is a foreign, but visible person, there could be manifests
         * meant for other foreign managers.
         * If the current user is a superuser, get everything. Otherwise
         * the userId must match either supervisorId or ManagerId
         */
        $query = $this->Manifests
            ->find('all')
            ->where([
                'OR' => [
                    'supervisor_member' => $person_id,
                    'manager_member' => $person_id,
                    'member_id' => $person_id
                ]
            ]);
        $manifests = $this->Manifests->configureLinkLayer($query);

        $stack->set(['manifests' => $manifests]);
        return $stack;
    }

    /**
     * Find non-self managers a supervisor has delegated
     *
     * @param $supervisor_id
     * @return StackSet of PersonCards
     */
    public function findDelegatedManagers($query, $options)
    {
        //@todo validate supervisor id. This could be an api call
        $supervisor_id = $options['supervisor_id'];
        /* @var ManifestsTable $Manifests */
        $Manifests = TableRegistry::getTableLocator()->get('Manifests');
        $delegates = $Manifests->find('list', ['valueField' => 'manager_member'])
            ->where([
                'supervisor_id' => $supervisor_id,
                'manager_id !=' => $supervisor_id
            ]);
        return $this->stacksFor('identity', $delegates->toArray());
    }
}

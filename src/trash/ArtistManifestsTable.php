<?php
namespace App\Model\Table;

use App\Model\Table\StacksTable;


/**
 * CakePHP ArtistManifestsTable
 * @author dondrake
 */
class ArtistManifestsTable extends StacksTable {

	/**
	 * {@inheritdoc}
	 */
	protected $rootName = 'identity';

	protected $rootTable = 'Identities';

	/**
	 * {@inheritdoc}
	 */
	protected $rootDisplaySource = 'name';

	public function initialize(array $config) {
		$this->setTable('manifests');
	    $this->addLayerTable(['Manifests', 'Identities', 'Permissions', 'DataOwners']);
		$this->addSeedPoint([
			'data_owner',
			'manager',
			'managers',
			'manifest',
			'manifests',
			'identity',
			'identities',
			'permission',
			'permissions'
		]);
		$this->addStackSchema([
			'identity',
			'data_owner',
			'manifests',
			'managers',
			'permissions'
		]);
		parent::initialize($config);
	}

	protected function distillFromIdentity($ids) {
		return $this->Identities->find('all')->where(['id IN' => $ids]);
	}

	protected function distillFromManager($ids) {
		return $this->Manifests->find('list', ['fieldValue' => 'member_id'])
				->find('managedBy', ['ids' => $ids]);
	}

	protected function distillFromDataOwner($ids) {
		$IDs = $this->Manifests->find('list', ['fieldValue' => 'member_id'])
				->find('issuedBy', ['ids' => $ids]);
		return array_unique($IDs);
	}

	protected function distillFromManifest($ids) {
		$IDs = $this->Manifests->find('list', ['fieldValue' => 'member_id'])
				->where(['id IN' => $ids]);
		return array_unique($IDs);
	}

	protected function distillFromPermission($ids) {
		$manifest_ids = $this->Permissions->find('list', ['fieldValue' => 'manifest_id'])
				->where(['id IN' => $ids]);
		return $this->distillFromManifest($manifest_ids);
	}

	protected function marshalIdentity($id, $stack) {
			$identity = $this->Identities
                ->find('all')
                ->where(['id' => $id]);
			$stack->set(['identity' => $identity->toArray()]);
			return $stack;
	}

	protected function marshalManifests($id, $stack) {
		if ($stack->count('identity')) {
			$manifests = $this->Manifests
                ->find('all')
                ->where(['member_id' => $id]);
			$stack->set(['manifests' => $manifests->toArray()]);
		}
		return $stack;
	}

	protected function marshalDataOwner($id, $stack) {
		if ($stack->count('identity')) {
			$dataOwner = $this->DataOwners
					->find('hook')
					->where(['id' => $stack->dataOwnerId()]);
			$stack->set(['data_owner' => $dataOwner->toArray()]);
		}
		return $stack;
	}

	protected function marshalManagers($id, $stack) {
		if ($stack->count('identity')) {
			$manager_ids = $stack->manifests->toValueList('manager_id');
			$managers = $this->DataOwners
					->find('hook')
					->where(['id IN' => $manager_ids]);
			$stack->set(['managers' => $managers->toArray()]);
		}
		return $stack;
	}

	protected function marshalPermissions($id, $stack) {
		if ($stack->count('identity')) {
			$manifest_ids = $stack->manifests->toValueList('id');
			$permissions = $this->Permissions
                ->find('all')
                ->where(['manifest_id IN' => $manifest_ids]);
			$stack->set(['permissions' => $permissions->toArray()]);
		}
		return $stack;
	}

}

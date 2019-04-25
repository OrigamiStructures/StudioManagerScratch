<?php
namespace App\Model\Table;

use App\Model\Table\StacksTable;


/**
 * CakePHP ArtistManifestTable
 * @author dondrake
 */
class ArtistManifestsTable extends StackTable {
	
	/**
	 * {@inheritdoc}
	 */
	protected $rootName = 'identity';
	
	/**
	 * {@inheritdoc}
	 */
	protected $rootDisplaySource = 'name';

	public function initialize(array $config) {
		$this->setTable('manifests');
		parent::initialize($config);
	    $this->addLayerTable(['Identity', 'Permissions', 'DataOwner']);
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
	}
	
	public function distillFromManager($ids) {
		$IDs = $this->Manifests->find('list', ['fieldValue' => 'member_id'])
				->find('managedBy', ['ids' => $ids]);
		return array_unique($IDs);
	}
	
	public function distillFromDataOwner($ids) {
		$IDs = $this->Manifests->find('list', ['fieldValue' => 'member_id'])
				->find('issuedBy', ['ids' => $ids]);
		return array_unique($IDs);
	}
	
	public function distillFromManifest($ids) {
		$IDs = $this->Manifests->find('list', ['fieldValue' => 'member_id'])
				->where(['id IN' => $ids]);
		return array_unique($IDs);
	}
	
	public function distillFromIdentity($ids) {
		$IDs = $this->Manifests->find('list', ['fieldValue' => 'member_id'])
				->find('manifestsFor', ['ids' => $ids]);
		return array_unique($IDs);
	}
	
	public function distillFromPermission($ids) {
		$manifest_ids = $this->Permissions->find('list', ['fieldValue' => 'manifest_id'])
				->where(['id IN' => $ids]);
		return $this->distillFromManifest($manifest_ids);
	}
	
	
	public function marshalIdentity($id, $stack) {
			$identity = $this->Identities
                ->find('all')
                ->where(['id' => $id]);
			$stack->set(['identity' => $identity->toArray()]);
			return $stack;
	}
	
	public function marshalManifests($id, $stack) {
		if ($stack->count('identity')) {
			$manifests = $this->Manifests
                ->find('all')
                ->where(['member_id' => $id]);
			$stack->set(['manifests' => $manifests->toArray()]);
		}
		return $stack;
	}
	
	public function marshalDataOwner($id, $stack) {
		if ($stack->count('identity')) {
			$dataOwner = $this->DataOwners
					->find('hook')
					->where(['id' => $stack->dataOwner()]);
			$stack->set(['data_owner' => $dataOwner->toArray()]);
		}
		return $stack;
	}
	
	public function marshalManagers($id, $stack) {
		if ($stack->count('manifest')) {
			$manager_ids = $stack->manifests->valueList('manager_id');
			$managers = $this->DataOwners
					->find('hook')
					->where(['id IN' => $manager_ids]);
			$stack->set(['managers' => $managers->toArray()]);
		}		
		return $stack;
	}
	
	public function marshalPermissions($id, $stack) {
		if ($stack->count('identity')) {
			$manifest_ids = $stack->manifests->valueList('id');
			$permissions = $this->Permissions
                ->find('all')
                ->where(['manifest_id IN' => $manifest_ids]);
			$stack->set(['permissions' => $permissions->toArray()]);
		}
		return $stack;
	}
	
}

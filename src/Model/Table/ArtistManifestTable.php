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
	
	public function marshalDataOwner($id, $stack) {
		
	}
	
	public function marshalManager($id, $stack) {
		
	}
	
	public function marshalMaifest($id, $stack) {
		
	}
	
	public function marshalArtist($id, $stack) {
		
	}
	
	public function marshalPermissions($id, $stack) {
		
	}
	
}

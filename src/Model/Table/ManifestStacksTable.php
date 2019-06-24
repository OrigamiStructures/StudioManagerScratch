<?php
namespace App\Model\Table;

use App\Model\Table\StacksTable;
use App\Model\Lib\Layer;

/**
 * Description of ManifestStacksTable
 *
 * @author dondrake
 */
class ManifestStacksTable extends StacksTable {
	
	/**
	 * {@inheritdoc}
	 */
	protected $rootName = 'manifest';
	
	/**
	 * {@inheritdoc}
	 */
	public $rootDisplaySource = 'id';

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config) {
        $this->setTable('manifests');
        $this->addLayerTable(['Manifests', 'PersonCards', 'Permissions']);
        $this->addStackSchema(['manifest', 'permissions']);
        $this->addSeedPoint([
            'manifest',
            'manifests',
            'artist',
            'artists',
            'manager',
            'managers',
            'supervisor',
            'supervisors',
			'permission',
			'permissions'
        ]);
		parent::initialize($config);
	}
	
	/**
	 * By id or array of IDs
	 * 
	 * @param \App\Model\Table\Query $query
	 * @param array $options
	 * @return array
	 */
	public function findManifests(Query $query, $options) {
        return $this->Manifests->findManifests($query, $options);
	}
		
	/**
	 * Derive the Manifest ids relevant to these manifest ids
	 * 
	 * @param array $ids Manifest ids
	 * @return StackSet
	     */
	protected function distillFromManifest(array $ids) {
		return $ids;
	}
	
	/**
	 * Derive the Manifest ids relevant to these Artists (Members)
	 * 
	 * @param array $ids Artist ids (member_id)
	 * @return array manifest ids
	 */
	protected function distillFromArtist(array $ids) {
		$manifests = $this->Manifests
				->find('forArtist', ['member_id' => $ids])
				->select(['id', 'member_id'])
			;
		$IDs = (new Layer($manifests))->IDs();
		return $IDs;
	}
	
	protected function distillFromPermission($ids) {
		
	}
	/**
	 * Derive the Manifest ids relevant to these Managers
	 * 
	 * @param array $ids Manager ids
	 * @return array manifest ids
	 */
	protected function distillFromManager(array $ids) {
		$manifests = $this->Manifests
				->find('managedBy', ['ids' => $ids])
				->select(['id', 'manager_id'])
			;
		$IDs = (new Layer($manifests))->IDs();
		return $IDs;
	}
	
	/**
	 * Derive the Manifest ids relevant to these Supervisors
	 * 
	 * @param array $ids Supervisor ids
	 * @return array manifest ids
	 */
	protected function distillFromSupervisor(array $ids) {
		$manifests = $this->Manifests
				->find('issuedBy', ['ids' => $ids])
				->select(['id', 'supervisor_id'])
			;
		$IDs = (new Layer($manifests))->IDs();
		return $IDs;
	}
	
	/**
	 * Marshal the manifest layer of this object
	 * 
	 * @param string $id
	 * @param StackEntity $stack
	 * @return StackEntity
	 */
	protected function marshalManifest($id, $stack) {
			$manifest = $this->Manifests->find('manifests', ['values' => [$id]]);
			$stack->set(['manifest' => $manifest->toArray()]);
			$stack = $this->marshalNameCards($stack);
			return $stack;
	}
	
	/**
	 * Marshal the permissions for the manifest
	 * 
	 * @todo BUSINESS LOGIC REQUIRED
	 *		If the current user is not this manifest's supervisor or 
	 *		manager, the permissions should be left empty
	 * 
	 * @param string $id
	 * @param StackEntity $stack
	 * @return StackEntity
	 */
	protected function marshalPermissions($id, $stack) {
		if(!$this->permissionsRequired($stack)) {
			return $stack;
		}
		$permissions = $this->Permissions
				->find('all')
				->where(['manifest_id' => $id]);
		$stack->set(['permissions' => $permissions->toArray()]);
		return $stack;
	}
	
	private function permissionsRequired($stack) {
		$management_token = $this->session->read('Auth.User.management_token');
		return $stack->manifest()->supervisorId() === $management_token
				|| $stack->manifest()->managerId() === $management_token;
	}
	
	protected function marshalNameCards($stack) {
		
		$stack->manifest
				->find('permissions')
				->specifyFilter('layer', 'contact')
				->load();
		
		$manifest = $stack->manifest->element(0, LAYERACC_INDEX);
		$people = $this->PersonCards->processSeeds(
				[
					'supervisor' => [$manifest->supervisorId()],
					'manager' => [$manifest->managerId()],
					'identity' => [$manifest->artistId()]
				]
			);
		$stack->people = $people;
		return $stack;
	}
	
}

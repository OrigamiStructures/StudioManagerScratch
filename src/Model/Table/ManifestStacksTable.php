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
	
	protected $rootTable = 'Manifests';

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
	 * Derive the Manifest ids relevant to these manifest ids
	 * 
	 * @param array $ids Manifest ids
	 * @return StackSet
	     */
	protected function distillFromManifest(array $ids) {
		return $this->Manifests
				->find('all')
				->where(['id IN' => $ids])
			;
	}
	
	/**
	 * Derive the Manifest ids relevant to these Artists (Members)
	 * 
	 * @param array $ids Artist ids (member_id)
	 * @return array manifest ids
	 */
	protected function distillFromArtist(array $ids) {
		return $this->Manifests
				->find('forArtists', ['member_id' => $ids])
				->select(['id', 'member_id'])
			;
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
		return $this->Manifests
				->find('managedBy', ['ids' => $ids])
				->select(['id', 'manager_id'])
			;
	}
	
	/**
	 * Derive the Manifest ids relevant to these Supervisors
	 * 
	 * @param array $ids Supervisor ids
	 * @return array manifest ids
	 */
	protected function distillFromSupervisor(array $ids) {
		return $this->Manifests
				->find('issuedBy', ['ids' => $ids])
				->select(['id', 'supervisor_id'])
			;
	}
	
	/**
	 * Inject appropriate boundary conditions for this user/context
	 * 
	 * I think this may grow a little more complex than this example. 
	 * Controller/action context may be a consideration but we won't have 
	 * that information here. The `contextUser` object may be our 
	 * tool to communicate situational knowledge.
	 * 
	 * @param Query $query
	 * @param array $options none supported at this time
	 */
	protected function localConditions($query, $options = []) {
		return $query->where([
			'user_id' => $this->currentUser()->userId(),
			'member_id IS NOT NULL'
			]);
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
		$management_token = $this->currentUser()->managerId();
		return $stack->manifest()->supervisorId() === $management_token
				|| $stack->manifest()->managerId() === $management_token;
	}
	
	protected function marshalNameCards($stack) {
		
		$stack->manifest
				->find('permissions')
				->specifyFilter('layer', 'contact')
				->load();
		
		$manifest = $stack->rootElement();
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
	
	/**
	 * Get the supervisors manifests
	 * 
	 * Options allowed
	 * ['source' => 'currentUser']
	 * ['source' => 'contextuser']
	 * ['ids' => [1, 6, 9]
	 * 
	 * sample call
	 * $ManifestStacks->find('supervisorManifests', ['source' => 'currentUser');
	 * 
	 * @todo Could anyone except a Superuser use the 'ids' option?
	 *		Depending on what our api callpoints allow and how they call 
	 *		methods like this we may need to do currentUser()->isSuperuser() 
	 *		checks to cut off crazy access
	 * 
	 * @param Query $query
	 * @param array $options
	 * @return StackSet
	 * @throws \BadMethodCallException
	 */
	public function findSupervisorManifests($query, $options) {
		if (
				key_exists('source', $options) 
				&& (in_array($options['source'], ['currentUser', 'contextUser']))
		) {
			$ids = [$this->{$options['source']}->supervisorId()];
		} elseif (key_exists('ids', $options)) {
			$ids = $options['ids'];
		} else {
			$msg = 'Allowed $options keys: "source" or "ids". "source" values: '
					. '"currentUser" or "contextUser". "ids" value must '
					. 'be an array of ids.';
			throw new \BadMethodCallException($msg);
		}
		return $this->find('stacksFor', ['seed' => 'supervisor', 'ids' => $ids]);
	}
	
	public function findManagerManifests($query, $options) {
		if (
				key_exists('source', $options) 
				&& (in_array($options['source'], ['currentUser', 'contextUser']))
		) {
			$ids = [$this->{$options['source']}->managerId()];
		} elseif (key_exists('ids', $options)) {
			$ids = $options['ids'];
		} else {
			$msg = 'Allowed $options keys: "source" or "ids". "source" values: '
					. '"currentUser" or "contextUser". "ids" value must '
					. 'be an array of ids.';
			throw new \BadMethodCallException($msg);
		}
		return $this->find('stacksFor', ['seed' => 'manager', 'ids' => $ids]);
	}
	
}

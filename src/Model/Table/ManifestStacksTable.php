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
			$stack = $this->composeNameCards($stack);
			return $stack;
	}
	
	protected function marshalPermissions($id, $stack) {
		return $stack;
	}
	
	protected function composeNameCards($stack) {
		$manifest = $stack->manifest->element(0, LAYERACC_INDEX);
		
		$card = $this->getPersonCard('supervisor', $manifest->supervisorId());
		$stack->supervisorCard = $card;
		
		$card = $this->getPersonCard('manager', $manifest->managerId());
		$stack->managerCard = $card;
		
		$card = $this->getPersonCard('identity', $manifest->artistId());
		$stack->artistCard = $card;
		
		return $stack;
	}
	
	private function getPersonCard($seed, $id) {
		$personSet = $this->PersonCards
				->find('stacksFor', ['seed' => $seed, 'ids' => [$id]]);
		return $personSet->element(0, LAYERACC_INDEX);
	}
	
}

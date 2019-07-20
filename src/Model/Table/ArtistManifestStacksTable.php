<?php
namespace App\Model\Table;

use App\Model\Table\ManagerManifestStacksTable;
use App\Model\Lib\Layer;

/**
 * Description of ManifestStacksTable
 *
 * @author dondrake
 */
class ArtistManifestStacksTable extends ManagerManifestStacksTable {
	
	public function initialize(array $config) {
        $this->addSeedPoint([
            'artist',
            'artists',
            'manager'
        ]);
		parent::initialize($config);
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
     * @return Query $query
	 */
	protected function localConditions($query, $options = []) {
		return $query->where([
			'user_id' => $this->currentUser()->userId(),
			'member_id IS NOT NULL'
			]);
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
	
}

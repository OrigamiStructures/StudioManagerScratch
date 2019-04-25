<?php
namespace App\Model\Table;

use App\Model\Table\StacksTable;


/**
 * CakePHP ArtistManifestTable
 * @author dondrake
 */
class ArtistManifestTable extends StackTable {
	
	/**
	 * {@inheritdoc}
	 */
	protected $rootName = 'manifest';
	
	/**
	 * {@inheritdoc}
	 */
//	protected $rootDisplaySource = 'name';

	public function initialize(array $config) {
		$this->setTable('artists');
		parent::initialize($config);
	    $this->addLayerTable(['Members', 'Permissions', 'DataOwner']);
		$this->addSeedPoint([
			'data_owner', 
			'manager',
			'managers',
			'manifest', 
			'manifests',
			'artist',
			'artists',
			'permission',
			'permissions'
		]);
		$this->addStackSchema([
			'data_owner',
			'manager',
			'manifest',
			'artist',
			'permissions'
		]);
	}
	
	public function distillFromManager($ids) {
		
	}
	
	public function distillFromDataOwner($ids) {
		
	}
	
	public function distillFromManifest($ids) {
		
	}
	
	public function distillFromArtist($ids) {
//		$IDs = $this->Manifest->find('manifestFor', ['ids' => [$ids]]);
	}
	
	public function distillFromPermission($ids) {
		
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

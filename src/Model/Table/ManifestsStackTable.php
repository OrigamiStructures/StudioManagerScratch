<?php
namespace App\Model\Table;

use App\Model\Table\StacksTable;

/**
 * Description of ManifestsStackTable
 *
 * @author dondrake
 */
class ManifestsStackTable extends StacksTable {
	
	/**
	 * {@inheritdoc}
	 */
	protected $rootName; // = 'identity';
	
	/**
	 * {@inheritdoc}
	 */
	public $rootDisplaySource; // = 'name';

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config) {
        $this->setTable('manifests');
		$this->_initializeAssociations();
        $this->addLayerTable(['Manifests']);
        $this->addStackSchema(['manifest']);
        $this->addSeedPoint([
            'manifest',
            'manifests',
            'artist',
            'artists',
            'manager',
            'managers',
            'supervisor',
            'supervisors'
        ]);
		parent::initialize($config);
	}
	
	
	
}

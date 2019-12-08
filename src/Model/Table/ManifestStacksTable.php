<?php
namespace App\Model\Table;

use App\Model\Entity\Manifest;
use App\Model\Entity\StackEntity;
use App\Model\Lib\StackSet;
use App\Model\Table\StacksTable;
use App\Model\Lib\Layer;
use Cake\ORM\Query;
use PharIo\Manifest\UrlTest;

/**
 * Description of ManifestStacksTable
 *
 * Get the ManifestsStacks
 *
 * @property ManifestsTable $Manifests
 * @property PersonCardsTable $PersonCards
 * @property PermissionsTable $Permissions
 * @property MembersTable $Members
 *
 * @author dondrake
 */
class ManifestStacksTable extends StacksTable {

    const MANIFEST_FOREIGN = 'foreign';
    const MANIFEST_SELF = 'self';
    const MANIFEST_ANY = 'any';

    /**
     * @var string
     */
    protected $rootName = 'manifest';

    /**
     * @var string
     */
    protected $rootTable = 'Manifests';

    /**
     * @var string
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
        $this->addLayerTable(['Manifests', 'PersonCards', 'Permissions', 'Members']);
        $this->addStackSchema(['manifest', 'permissions']);
        $this->addSeedPoint([
            'artist',
            'artists',
            'manifest',
            'manifests',
            'manager',
            'managers',
            'supervisor',
            'supervisors',
			'permission',
			'permissions'
        ]);
		parent::initialize($config);
	}

    //<editor-fold desc="*********************** DISTILLERS ****************************">
    /**
	 * Derive the Manifest ids relevant to these manifest ids
	 *
	 * @param array $ids Manifest ids
	 * @return Query
	     */
	protected function distillFromManifest(array $ids) {
		return $this->Manifests
				->find('all')
				->where(['id IN' => $ids])
			;
	}

	protected function distillFromPermission($ids) {
        osd('distilling permission');
        $manifest_ids = $this->Permissions->find('list', ['fieldValue' => 'manifest_id'])
            ->where(['id IN' => $ids]);
        return $this->distillFromManifest($manifest_ids);
	}
	/**
	 * Derive the Manifest ids relevant to these Managers
	 *
	 * @param array $ids Manager ids
	 * @return Query
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
	 * @return Query
	 */
	protected function distillFromSupervisor(array $ids) {
//	    osd($this->Manifests);die;
		return $this->Manifests
				->find('issuedBy', ['ids' => $ids])
				->select(['id', 'supervisor_id'])
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
    //</editor-fold>

    //<editor-fold desc="***************************** MARSHALLERS ***************************************">
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
//			osd($manifest->toArray());die;
			$stack = $this->marshalNameCards($stack);
			return $stack;
	}

	/**
	 * Marshal the permissions for the manifest
	 *
	 * @todo BUSINESS LOGIC REQUIRED
	 *      If the current user is not this manifest's supervisor or
	 *	    manager, the permissions should be left empty. BUT I don't
     *      think that circumstance is possible.
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
		$management_token = $this->contextUser()->getId('manager');
		return $stack->manifest()->supervisorId() === $management_token
				|| $stack->manifest()->managerId() === $management_token;
	}

    /**
     * @param $stack StackEntity
     * @return mixed
     */
	protected function marshalNameCards($stack) {

//		$stack->manifest
//				->find('permissions')
//				->specifyFilter('layer', 'contact')
//				->load();

        /* @var Manifest $manifest */

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
    //</editor-fold>

	/**
	 * Issued Manifests (this user is Supervisor/Issuer)
	 *
	 * @return StackSet
	 * @throws \BadMethodCallException
	 */
	public function ManifestsIssued($receiver = self::MANIFEST_ANY) {
        /* @var Layer $foreign */
        /* @var Layer $self */
        $userId = $this->currentUser()->userId();
        switch ($receiver) {
            case self::MANIFEST_ANY:
                $ids = [$userId];
                break;
            case self::MANIFEST_FOREIGN:
                $foreign = layer($this->Manifests->find('all', [
                        'where' => ['supervisor_id' => $userId, 'manager_id !=' => $userId],
                        'select' => ['id', 'supervisor_id', 'manager_id']
                    ]
                ));
                $ids = [$foreign->toDistinctList('manager_id')];
                break;
            case self::MANIFEST_SELF:
                $self = layer($this->Manifests->find('all', [
                        'where' => ['supervisor_id' => $userId, 'manager_id' => $userId],
                        'select' => ['id', 'supervisor_id', 'manager_id']
                    ]
                ));
                $ids = [$self->toDistinctList('manager_id')];
                break;
            default:
                $ids = [];
        }
		return $this->find('stacksFor', ['seed' => 'supervisor', 'ids' => $ids]);
	}

    /**
     * Recieved Manifests (this user is Manager)
     * @return StackSet
     * @throws \BadMethodCallException
     */
    public function ManifestsRecieved($issuer = self::MANIFEST_ANY) {
        /* @var Layer $foreign */
        /* @var Layer $self */
        $userId = $this->currentUser()->userId();
        switch ($issuer) {
            case self::MANIFEST_ANY:
                $ids = [$this->currentUser()->userId()];
                break;
            case self::MANIFEST_FOREIGN:
                $foreign = layer($this->Manifests->find('all', [
                    'where' => ['supervisor_id !=' => $userId, 'manager_id' => $userId],
                    'select' => ['id', 'supervisor_id', 'manager_id']
                    ]
                ));
                $ids = [$foreign->toDistinctList('manager_id')];
                break;
            case self::MANIFEST_SELF:
                $self = layer($this->Manifests->find('all', [
                        'where' => ['supervisor_id' => $userId, 'manager_id' => $userId],
                        'select' => ['id', 'supervisor_id', 'manager_id']
                    ]
                ));
                $ids = [$self->toDistinctList('manager_id')];
                break;
            default:
                $ids = [];
        }
		return $this->find('stacksFor', ['seed' => 'manager', 'ids' => $ids]);
	}

    /**
     * All Manifests (this user is Supervisor or Manager)
     *
     * @return StackSet
     * @throws \BadMethodCallException
     */
    public function AllManifests()
    {
        $id = $this->currentUser()->userId();
        return $this->processSeeds(['supervisor' => [$id], 'manager' => [$id]]);
	}

}

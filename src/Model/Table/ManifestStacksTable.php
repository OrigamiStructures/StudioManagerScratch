<?php
namespace App\Model\Table;

use App\Model\Entity\Manifest;
use App\Model\Entity\ManifestStack;
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
//		if(!$this->permissionsRequired($stack)) {
//			$permissions = [];
//		} else {
            $permissions = $this->Permissions
                ->find('all')
                ->where(['manifest_id' => $id])
                ->toArray();

//        }
		$stack->set(['permissions' => $permissions]);
		return $stack;
	}

    /**
     * @param $stack ManifestStack
     * @return bool
     */
	private function permissionsRequired($stack) {
		$management_token = $this->contextUser()->getId('manager');
        osd($stack->manifest()->getOwnerId('supervisor'));
        osd($stack->manifest()->getOwnerId('manager'));
        osd($management_token);
        osd($stack->manifest());
		return $stack->manifest()->getOwnerId('supervisor') === $management_token
				|| $stack->manifest()->getOwnerId('manager') === $management_token;
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
					'supervisor' => [$manifest->getOwnerId('supervisor')],
					'manager' => [$manifest->getOwnerId('manager')],
                    'identity' => [$manifest->getMemberId('artist')]
				]
			);
		$stack->people = $people;
		return $stack;
	}
    //</editor-fold>

	/**
	 * A set/subset of Manifests Issued by a supervisor
     *
     * All of them (any), management delegations (foreign), or self management (self)
     *
     * @param $supervisor_id string ID of the supervisor who issued the manifests
     * @param $recipients string 'any', 'foreign', 'self' filter
	 * @return StackSet
	 * @throws \BadMethodCallException
	 */
	public function ManifestsIssued($supervisor_id, $recipients = self::MANIFEST_ANY) {
        /* @var Layer $foreign */
        /* @var Layer $self */
        switch ($recipients) {
            case self::MANIFEST_ANY:
                $ids = [$supervisor_id];
                break;
            case self::MANIFEST_FOREIGN:
                $foreign = layer($this->Manifests->find('all', [
                        'where' => ['supervisor_id' => $supervisor_id, 'manager_id !=' => $supervisor_id],
                        'select' => ['id', 'supervisor_id', 'manager_id']
                    ]
                ));
                $ids = [$foreign->toDistinctList('manager_id')];
                break;
            case self::MANIFEST_SELF:
                $self = layer($this->Manifests->find('all', [
                        'where' => ['supervisor_id' => $supervisor_id, 'manager_id' => $supervisor_id],
                        'select' => ['id', 'supervisor_id', 'manager_id']
                    ]
                ));
                $ids = [$self->toDistinctList('manager_id')];
                break;
            default:
                $ids = [];
        }
        return $this->stacksFor('supervisor', $ids);
	}

    /**
     * A set/subset of Manifests Recieved by a manager
     *
     * All of them (any), delegations received (foreign), or self management (self)
     *
     * @param $issuer_id string ID of the supervisor who issued the manifests
     * @param $recipients string 'any', 'foreign', 'self' filter
     * @return StackSet
     * @throws \BadMethodCallException
     */
    public function ManifestsRecieved($manager_id, $issuer = self::MANIFEST_ANY) {
        /* @var Layer $foreign */
        /* @var Layer $self */
        switch ($issuer) {
            case self::MANIFEST_ANY:
                $ids = [$manager_id];
                break;
            case self::MANIFEST_FOREIGN:
                $foreign = layer($this->Manifests->find('all', [
                    'where' => ['supervisor_id !=' => $manager_id, 'manager_id' => $manager_id],
                    'select' => ['id', 'supervisor_id', 'manager_id']
                    ]
                ));
                $ids = [$foreign->toDistinctList('manager_id')];
                break;
            case self::MANIFEST_SELF:
                $self = layer($this->Manifests->find('all', [
                        'where' => ['supervisor_id' => $manager_id, 'manager_id' => $manager_id],
                        'select' => ['id', 'supervisor_id', 'manager_id']
                    ]
                ));
                $ids = [$self->toDistinctList('manager_id')];
                break;
            default:
                $ids = [];
        }
		return $this->stacksFor('manager', $ids);
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

<?php
namespace App\Model\Table;

use App\Model\Entity\Manifest;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use App\Model\Table\AppTable;
use Cake\Validation\Validator;
use App\Model\Behavior\IntegerQueryBehavior;
use http\Exception\BadMethodCallException;

/**
 * Manifests Model
 *
 * @property \App\Model\Table\MembersTable|\Cake\ORM\Association\BelongsTo $Members
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\MemberUsersTable|\Cake\ORM\Association\BelongsTo $MemberUsers
 *
 * @method IntegerQueryBehavior integer(Query $query, $column, $params);
 *
 */
class ManifestsTable extends AppTable{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('manifests');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

		$this->_initializeBehaviors();

        $this->belongsTo('Members', [
            'foreignKey' => 'member_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'artist_id'
        ]);
    }

    protected function _initializeBehaviors() {
        $this->addBehavior('Timestamp');
        $this->addBehavior('IntegerQuery');
    }

	/**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', 'create');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        $rules->add($rules->existsIn(['supervisor_member'], 'Members'));
        $rules->add($rules->existsIn(['manager_member'], 'Members'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['supervisor_id'], 'Users'));
        $rules->add($rules->existsIn(['manager_id'], 'Users'));

        return $rules;
    }

    /**
     * Find artists by id
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findManifests($query, $options) {
        return $this->integer($query, 'id', $options['values']);
    }

	/**
	 * Find a manager's artist manifests (or those for several managers)
	 *
	 * Manifests::find('managedBy', ['ids' => [x,y])
	 * Manifests::find('managedBy', ['id' => x])
	 *
	 * @param type $query
	 * @param type $options
	 */
	public function findManagedBy($query, $options) {
		if (array_key_exists('ids', $options)) {
			$condition = ['Manifests.manager_id IN' => $options['ids']];
		} elseif (array_key_exists('id', $options)) {
			$condition = ['Manifests.manager_id IN' => $options['ids']];
		} else {
			$msg = 'You must include \'ids\'=>[x,y] or '
					. '\'id\'=>x in your options array.';
			throw new \BadMethodCallException($msg);
		}
		return $query->where($condition);
	}

	/**
	 * Find the artist manifests issued by a user (or several users)
	 *
	 * Manifests::find('issuedBy', ['ids' => [x,y])
	 * Manifests::find('issuedBy', ['id' => x])
	 *
	 * @param type $query
	 * @param type $options
	 */
	public function findIssuedBy($query, $options) {
		if (array_key_exists('ids', $options)) {
			$condition = ['Manifests.supervisor_id IN' => $options['ids']];
		} elseif (array_key_exists('id', $options)) {
			$condition = ['Manifests.supervisor_id IN' => $options['ids']];
		} else {
			$msg = 'You must include \'ids\'=>[x,y] or '
					. '\'id\'=>x in your options array.';
			throw new \BadMethodCallException($msg);
		}
		return $query->where($condition);
	}

	/**
	 * Find the manifests for a member/artist (or several member/artists)
	 *
	 * Manifests::find('manifestFor', ['ids' => [x,y])
	 * Manifests::find('manifest', ['id' => x])
	 *
	 * @param type $query
	 * @param type $options
	 */
	public function findForArtists($query, $options) {
		if (array_key_exists('ids', $options)) {
			$condition = ['Manifests.member_id IN' => $options['ids']];
		} elseif (array_key_exists('id', $options)) {
			$condition = ['Manifests.member_id IN' => $options['ids']];
		} else {
			$msg = 'You must include \'ids\'=>[x,y] or '
					. '\'id\'=>x in your options array.';
			throw new \BadMethodCallException($msg);
		}
		return $query->where($condition);
	}

    /**
     * Distill a set of manifests to an id => name list from Members
     *
     * These will be the names referenced anywhere in the manifests keyed by member_id
     *
     * @param $query
     * @param $options array ['manifests' => array of manifest entities]
     */
    public function findNameOfParticipants($query, $options)
    {
        if(!key_exists('manifests', $options)) {
            $msg = 'The find("nameOfParticipants") $options must be an array: ["manifests" => [ManifestEntity, ManEnt, ...]]';
            throw new \BadMethodCallException($msg);
        }
        $manifests = collection($options['manifests']);
        $ids = $manifests->reduce(function($accum, $manifest) {
            /* @var Manifest $manifest */
            $accum[] = $manifest->getManagerMember();
            $accum[] = $manifest->getSupervisorMember();
            $accum[] = $manifest->artistId();
            return $accum;
        }, []);
        $memberIds = array_unique($ids);
        $members = $this->Members->find('Members', ['values' => $memberIds])->toArray();
        $nameList = (layer($members))->toKeyValueList('id', 'name');
        return $nameList;
    }
}

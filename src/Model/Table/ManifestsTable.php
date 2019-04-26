<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use App\Model\Table\AppTable;
use Cake\Validation\Validator;
use App\Model\Behavior\IntegerQueryBehavior;

/**
 * Artists Model
 *
 * @property \App\Model\Table\MembersTable|\Cake\ORM\Association\BelongsTo $Members
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\MemberUsersTable|\Cake\ORM\Association\BelongsTo $MemberUsers
 *
 * @method \App\Model\Entity\Manifest get($primaryKey, $options = [])
 * @method \App\Model\Entity\Manifest newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Manifest[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Manifest|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Manifest|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Manifest patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Manifest[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Manifest findOrCreate($search, callable $callback = null, $options = [])
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
            ->allowEmpty('id', 'create');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['member_user_id'], 'MemberUsers'));

        return $rules;
    }
	
    /**
     * Find artists by id
     * 
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findArtists($query, $options) {
        return $this->integer($query, 'id', $options['values']);
    }
    
    /**
     * Find members
     * 
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findInMembers($query, $options) {
        return $this->integer($query, 'member_id', $options['values']);
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
			$condition = ['Manifests.user_id IN' => $options['ids']];
		} elseif (array_key_exists('id', $options)) {
			$condition = ['Manifests.user_id IN' => $options['ids']];
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
	public function findManifestsFor($query, $options) {
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
	
}

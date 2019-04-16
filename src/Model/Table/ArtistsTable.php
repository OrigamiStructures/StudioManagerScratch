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
 * @method \App\Model\Entity\Artist get($primaryKey, $options = [])
 * @method \App\Model\Entity\Artist newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Artist[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Artist|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Artist|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Artist patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Artist[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Artist findOrCreate($search, callable $callback = null, $options = [])
 */
class ArtistsTable extends AppTable{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('artists');
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
        $this->belongsTo('MemberUsers', [
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
}

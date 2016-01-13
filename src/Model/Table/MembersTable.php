<?php
namespace App\Model\Table;

use App\Model\Entity\Member;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Members Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Images
 * @property \Cake\ORM\Association\HasMany $Dispositions
 * @property \Cake\ORM\Association\HasMany $Locations
 * @property \Cake\ORM\Association\HasMany $Users
 * @property \Cake\ORM\Association\BelongsToMany $Groups
 */
class MembersTable extends AppTable
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('members');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Images', [
            'foreignKey' => 'image_id'
        ]);
        $this->hasMany('Dispositions', [
            'foreignKey' => 'member_id'
        ]);
        $this->hasMany('Locations', [
            'foreignKey' => 'member_id'
        ]);
        $this->hasMany('Addresses', [
            'foreignKey' => 'member_id'
        ]);
        $this->hasMany('Contacts', [
            'foreignKey' => 'member_id'
        ]);
        $this->hasOne('Users', [
            'foreignKey' => 'member_id'
        ]);
        $this->belongsToMany('Groups', [
            'foreignKey' => 'member_id',
            'targetForeignKey' => 'group_id',
            'joinTable' => 'groups_members'
        ]);
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
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('name');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['image_id'], 'Images'));
        return $rules;
    }
    
    /**
     * Custom finder for the member review action
     * 
     * Returns either a list of members or a specific member based upon the existance
     * of the member query argument
     * 
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findMemberReview(Query $query, array $options) {
        $query = $this->findMemberList($query, $options);
        if($this->SystemState->isKnown('member')){
            $query->where([
               'Member.id' => $this->SystemState->queryArg('member')
            ]);
        }
        return $query;
    }
    
    /**
     * Custom finder for the memberList
     * 
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findMemberList(Query $query, array $options) {
        $query->where([
            'Member.active' => 1,
            'Member.user_id' => $this->SystemState->artistId()
        ]);
        return $query;
    }
}

<?php
namespace App\Model\Table;

use App\Model\Entity\Group;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Groups Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsToMany $Members
 * @property \Cake\ORM\Association\HowOne $Members
 */
class GroupsTable extends AppTable
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

        $this->table('groups');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Members', [
            'className' => 'Members',
            'foreignKey' => 'id',
            'bindingKey' => 'member_id',
            'dependent' => TRUE
        ]);
        $this->belongsToMany('Members', [
            'foreignKey' => 'group_id',
            'targetForeignKey' => 'member_id',
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
        return $rules;
    }
    
    /**
     * Find all the active groups belonging to the logged in user
     * 
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findMyGroups(Query $query, array $options) {
        $query->where([
            'Groups.active' => 1,
            'Groups.user_id' => $this->SystemState->artistId()
        ]);
        $query->contain(['Members']);
        osd($query->toArray());
        return $query;
    }
    
    /**
     * Find groups associated with this SystemState member, filtered by basic
     * findMyGroups search
     * 
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findMemberGroups(Query $query, array $options) {
        $query = $this->findMyGroups($query, $options);
//        $query->where([
//            'Members.id' => $this->SystemState->queryArg('member')
//        ]);
        return $query;
    }
}

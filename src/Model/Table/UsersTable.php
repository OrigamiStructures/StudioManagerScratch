<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Members
 * @property \Cake\ORM\Association\HasMany $Artworks
 * @property \Cake\ORM\Association\HasMany $Dispositions
 * @property \Cake\ORM\Association\HasMany $Editions
 * @property \Cake\ORM\Association\HasMany $Formats
 * @property \Cake\ORM\Association\HasMany $Groups
 * @property \Cake\ORM\Association\HasMany $GroupsMembers
 * @property \Cake\ORM\Association\HasMany $Images
 * @property \Cake\ORM\Association\HasMany $Locations
 * @property \Cake\ORM\Association\HasMany $Members
 * @property \Cake\ORM\Association\HasMany $Pieces
 * @property \Cake\ORM\Association\HasMany $Series
 * @property \Cake\ORM\Association\HasMany $Subscriptions
 */
class UsersTable extends AppTable
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

        $this->table('users');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Members', [
            'foreignKey' => 'member_id'
        ]);
        $this->hasMany('Artworks', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Dispositions', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Editions', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Formats', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Groups', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('GroupsMembers', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Images', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Locations', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Members', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Pieces', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Series', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Subscriptions', [
            'foreignKey' => 'user_id'
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
            ->allowEmptyString('username');

        $validator
            ->allowEmptyString('password');

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
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        return $rules;
    }
}

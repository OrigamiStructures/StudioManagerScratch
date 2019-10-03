<?php
namespace App\Model\Table;

use App\Model\Entity\Subscription;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Subscriptions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\HasMany $Formats
 */
class SubscriptionsTable extends AppTable
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

        $this->setTable('subscriptions');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

//        $this->belongsTo('Users', [
//            'foreignKey' => 'user_id'
//        ]);
//        $this->hasMany('Formats', [
//            'foreignKey' => 'subscription_id'
//        ]);
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
            ->allowEmptyString('title');

        $validator
            ->allowEmptyString('description');

//        $validator
//            ->add('range_flag', 'valid', ['rule' => 'numeric'])
//            ->allowEmpty('range_flag');
//
//        $validator
//            ->add('range_start', 'valid', ['rule' => 'numeric'])
//            ->allowEmpty('range_start');
//
//        $validator
//            ->add('range_end', 'valid', ['rule' => 'numeric'])
//            ->allowEmpty('range_end');

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
	 * Get the current select list
	 *
	 * @param Query $query
	 * @param string $artist_id
	 * @return query result object
	 */
	public function findChoiceList(Query $query, $options) {
		return $query->where(['user_id' => $options['artist_id']])->find('list');
	}

}

<?php
namespace App\Model\Table;

use App\Model\Entity\Format;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Formats Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Images
 * @property \Cake\ORM\Association\BelongsTo $Editions
 * @property \Cake\ORM\Association\BelongsTo $Subscriptions
 * @property \Cake\ORM\Association\HasMany $Pieces
 */
class FormatsTable extends AppTable
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

        $this->table('formats');
        $this->displayField('title');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
		$this->addBehavior('Family');
		$this->addBehavior('ArtworkStack');

//		if (!isset($this->SystemState) || $this->SystemState->is(ARTWORK_SAVE)) {
		if ($this->SystemState->is(ARTWORK_SAVE)) {
			$this->belongsTo('Users',
					[
				'foreignKey' => 'user_id',
			]);
		}		
        $this->belongsTo('Images', [
            'foreignKey' => 'image_id',
        ]);
        $this->belongsTo('Editions', [
            'foreignKey' => 'edition_id',
        ]);
        $this->belongsTo('Subscriptions', [
            'foreignKey' => 'subscription_id',
        ]);
        $this->hasMany('Pieces', [
            'foreignKey' => 'format_id',
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
            ->allowEmpty('title');

        $validator
            ->allowEmpty('description');

        $validator
            ->add('range_flag', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('range_flag');

        $validator
            ->add('range_start', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('range_start');

        $validator
            ->add('range_end', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('range_end');

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
        $rules->add($rules->existsIn(['edition_id'], 'Editions'));
        $rules->add($rules->existsIn(['subscription_id'], 'Subscriptions'));
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
		$this->displayField('display_title');
		return $query->where(['user_id' => $options['artist_id']])->find('list');
	}
	
}

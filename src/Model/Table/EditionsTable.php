<?php
namespace App\Model\Table;

use App\Model\Entity\Edition;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Editions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Artworks
 * @property \Cake\ORM\Association\HasMany $Formats
 * @property \Cake\ORM\Association\HasMany $Pieces
 */
class EditionsTable extends Table
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

        $this->table('editions');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Artworks', [
            'foreignKey' => 'artwork_id'
        ]);
        $this->hasMany('Formats', [
            'foreignKey' => 'edition_id'
        ]);
        $this->hasMany('Pieces', [
            'foreignKey' => 'edition_id'
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

        $validator
            ->allowEmpty('type');

        $validator
            ->add('quantity', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('quantity');

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
        $rules->add($rules->existsIn(['artwork_id'], 'Artworks'));
        return $rules;
    }
}

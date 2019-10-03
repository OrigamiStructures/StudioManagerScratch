<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use App\Model\Table\AppTable;
use Cake\Validation\Validator;

/**
 * DispositionsPieces Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\DispositionsTable|\Cake\ORM\Association\BelongsTo $Dispositions
 * @property \App\Model\Table\PiecesTable|\Cake\ORM\Association\BelongsTo $Pieces
 *
 * @method \App\Model\Entity\DispositionsPiece get($primaryKey, $options = [])
 * @method \App\Model\Entity\DispositionsPiece newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DispositionsPiece[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DispositionsPiece|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DispositionsPiece|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DispositionsPiece patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DispositionsPiece[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DispositionsPiece findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DispositionsPiecesTable extends AppTable
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

        $this->setTable('dispositions_pieces');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Dispositions', [
            'foreignKey' => 'disposition_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Pieces', [
            'foreignKey' => 'piece_id',
            'joinType' => 'INNER'
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
            ->integer('id')
            ->allowEmpty('id', 'create');

//        $validator
//            ->boolean('complete')
//            ->allowEmpty('complete');

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
        $rules->add($rules->existsIn(['disposition_id'], 'Dispositions'));
        $rules->add($rules->existsIn(['piece_id'], 'Pieces'));

        return $rules;
    }
}

<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Model\Table\AppTable;

/**
 * EditionsFormats Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\FormatsTable|\Cake\ORM\Association\BelongsTo $Formats
 * @property \App\Model\Table\EditionsTable|\Cake\ORM\Association\BelongsTo $Editions
 *
 * @method \App\Model\Entity\EditionsFormat get($primaryKey, $options = [])
 * @method \App\Model\Entity\EditionsFormat newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\EditionsFormat[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\EditionsFormat|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EditionsFormat|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EditionsFormat patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\EditionsFormat[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\EditionsFormat findOrCreate($search, callable $callback = null, $options = [])
 */
class EditionsFormatsTable extends AppTable
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

        $this->setTable('editions_formats');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Formats', [
            'foreignKey' => 'format_id'
        ]);
        $this->belongsTo('Editions', [
            'foreignKey' => 'edition_id'
        ]);
        $this->_initializeBehaviors();
    }


    protected function _initializeBehaviors() {
        $this->addBehavior('Timestamp');
        $this->addBehavior('IntegerQuery');
        $this->addBehavior('StringQuery');
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

        $validator
            ->integer('assigned_piece_count')
            ->allowEmpty('assigned_piece_count');

        $validator
            ->integer('fluid_piece_count')
            ->allowEmpty('fluid_piece_count');

        $validator
            ->integer('collected_piece_count')
            ->allowEmpty('collected_piece_count');

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
//        $rules->add($rules->existsIn(['format_id'], 'Formats'));
        $rules->add($rules->existsIn(['edition_id'], 'Editions'));

        return $rules;
    }
	
    /**
     * Find Formats by id
     * 
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findFormats($query, $options) {
        return $this->integer($query, 'id', $options['values']);
    }
    
    /**
     * Find in editions
     * 
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findInEditions($query, $options) {
        return $this->integer($query, 'edition_id', $options['values']);
    }
    
	
}

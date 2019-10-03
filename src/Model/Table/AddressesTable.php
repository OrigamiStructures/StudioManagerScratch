<?php
namespace App\Model\Table;

use App\Model\Entity\Address;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use ArrayObject;
use App\Model\Behavior\IntegerQueryBehavior;
use App\Model\Behavior\StringQueryBehavior;

/**
 * Addresses Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Members
 */
class AddressesTable extends AppTable
{

// <editor-fold defaultstate="collapsed" desc="Core">

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)     {
        parent::initialize($config);

        $this->table('addresses');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->_initializeBehaviors();

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Members', [
            'foreignKey' => 'member_id',
            'joinType' => 'INNER'
        ]);
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
    public function validationDefault(Validator $validator)     {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmptyString('address1')
            ->allowEmptyString('address2')
            ->allowEmptyString('address3')
            ->allowEmptyString('city')
            ->allowEmptyString('state')
            ->allowEmptyString('zip')
            ->allowEmptyString('country');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)     {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        return $rules;
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Lifecycle events">

    /**
     * Implemented beforeMarshal event
     *
     * @param \App\Model\Table\Event $event
     * @param \App\Model\Table\ArrayObject $data
     * @param \App\Model\Table\ArrayObject $options
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options) {
        $data['user_id'] = $this->SystemState->artistId();
    }

// </editor-fold>

    /**
     * Make the specified number of new Contact arrays (for TRD use)
     *
     * @param integer $count How many contacts are needed
     * @param array $default [column => value] to control what data the pieces have
     * @param integer $start The index (and number) of the first of the ($count) pieces
     * @return array An array of new entity column-value arrays
     */
    public function spawn($count, $default = [], $start = 0) {
            $columns = $default + [
                    'id' => NULL,
                    'user_id' => $this->SystemState->artistId(),
        'label' => 'New'
            ];

    return array_fill($start, $count, $columns);
    }

// <editor-fold defaultstate="collapsed" desc="Custom Finders">

    /**
     * Find addresses by id
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findAddresses($query, $options) {
        return $this->integer($query, 'id', $options['values']);
    }

    /**
     * Find members
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findInMembers(Query $query, $options) {
        return $this->integer($query, 'member_id', $options['values']);
    }

    /**
     * Find kind of address (label)
     *
     * @param Query $query
     * @param array $options see StringQueryBehavior
     * @return Query
     */
    public function findKind(Query $query, $options) {
        return $this->string($query, 'label', $options['value']);
    }

    /**
     * Find zip codes (strings not integers)
     *
     * @param Query $query
     * @param array $options see StringQueryBehavior
     * @return Query
     */
    public function findZipCodes(Query $query, $options) {
        return $this->string($query, 'zip', $options['value']);
    }

    /**
     * Find cities
     *
     * @param Query $query
     * @param array $options see StringQueryBehavior
     * @return Query
     */
    public function findCities(Query $query, $options) {
        return $this->string($query, 'city', $options['value']);
    }

    /**
     * Find zip codes (strings not integers)
     *
     * @param Query $query
     * @param array $options see StringQueryBehavior
     * @return Query
     */
    public function findStates(Query $query, $options) {
        return $this->string($query, 'state', $options['value']);
    }

// </editor-fold>

}

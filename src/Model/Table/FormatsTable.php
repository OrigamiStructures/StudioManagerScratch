<?php

namespace App\Model\Table;

use App\Model\Entity\Format;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Lib\Traits\EditionStackCache;
use App\Model\Behavior\IntegerQueryBehavior;

/**
 * Formats Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Images
 * @property \Cake\ORM\Association\BelongsTo $Editions
 * @property \Cake\ORM\Association\BelongsTo $Subscriptions
 * @property \Cake\ORM\Association\HasMany $Pieces
 */
class FormatsTable extends AppTable {

    use EditionStackCache;

// <editor-fold defaultstate="collapsed" desc="Core processes">

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);
        $this->_initializeProperities();
        $this->_initializeAssociations();
        $this->_initializeBehaviors();
    }

// <editor-fold defaultstate="collapsed" desc="Initialization methods">
    protected function _initializeProperities() {
        $this->setDisplayField('displayTitle');
    }

    protected function _initializeAssociations() {
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',]);
        $this->belongsTo('Images', [
            'foreignKey' => 'image_id',]);
        $this->belongsTo('Editions', [
            'foreignKey' => 'edition_id',]);
        $this->belongsTo('Subscriptions', [
            'foreignKey' => 'subscription_id',]);
        $this->hasMany('Pieces', [
            'foreignKey' => ['format_id', 'edition_id'],
            'bindingKey' => ['id', 'edition_id'],
        ]);
    }

    /**
     * @todo these behaviors need to be evaluated for use. At 
     *      least the Family behavior has not been used
     */
    protected function _initializeBehaviors() {
        $this->addBehavior('Timestamp');
        $this->addBehavior('Family');
        $this->addBehavior('ArtworkStack');
        $this->addBehavior('CounterCache', [
            'Editions' => ['format_count'],
        ]);
        $this->addBehavior('IntegerQuery');
    }

// </editor-fold>

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator) {
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
    public function buildRules(RulesChecker $rules) {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['image_id'], 'Images'));
        $rules->add($rules->existsIn(['edition_id'], 'Editions'));
        $rules->add($rules->existsIn(['subscription_id'], 'Subscriptions'));
        return $rules;
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Lifecycle Events">

    /**
     * After save, clear any effected edition stackQuery cache
     * 
     * @param type $event
     * @param type $entity
     * @param type $options
     */
    public function afterSave($event, $entity, $options) {
        if (!isset($entity->edition_id)) {
            $query = $this->find()
                    ->where(['id' => $entity->id, 'user_id' => $this->SystemState->artistId()])
                    ->select(['edition_id'])->toArray();
            $entity->edition_id = $query[0]->edition_id;
        }
        $this->clearCache($entity->edition_id);
        osdLog($entity, 'afterSave on this format entity');
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Choice List engines">

    /**
     * Get the current select list
     * 
     * @todo This named method is set up in many tables but I'm not 
     *      sure how much it's used in forms. This particular one doesn't 
     *      appear to be used at all. Also, this one is a bit iffy in terms 
     *      of the kind of list it would return. Would we really want the 
     *      choices from such a list? 
     * 
     * @param Query $query
     * @param string $artist_id
     * @return query result object
     */
    public function findChoiceList(Query $query, $options) {
        $this->setDisplayField('displayTitle');
        return $query->where(['user_id' => $options['artist_id']])
                ->distinct('description')->find('list');
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Custom Finders">
    
    public function findSoldOut($query, $options = []) {
        return $query->find('collectedPieceCount', ['value' => ['>', 0]])
            ->where(['collected_piece_count' => 'assigned_piece_count']);
    }
    
    public function findCollectedPieceCount($query, $options) {
        return $this->integer($query, 'collected_piece_count', $options['values']);
    }
    
    public function findCollectedPiecePercentage($query, $options) {
        return $this->integer($query, 
            'collected_piece_count / assigned_piece_count', 
            $options['values']);
    }
    
    public function inSubscription($query, $options) {
        return $this->integer($query, 'subscription_id', $options['values']);
    }
    
    public function findHasFluid($query, $options = []) {
        return $query->where(['fluid_piece_count >' => 0]);
    }
    
    public function findEmpty($query, $options = []) {
        return $query->where(['assigned_piece_count' => 0]);
    }
    
    /**
     * 
     * @param type $query
     * @param type $options
     */
    public function findSearch($query, $options) {
        
    }
        
// </editor-fold>

}

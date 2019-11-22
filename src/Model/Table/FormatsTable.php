<?php

namespace App\Model\Table;

use App\Model\Entity\Format;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Lib\Traits\EditionStackCache;
use App\Model\Behavior\IntegerQueryBehavior;
use App\Model\Behavior\StringQueryBehavior;

/**
 * Formats Model
 *
 * @todo if I need to add Table. to column names
 *      Regex
 *      search: integer\(\$query, '(.*)',
 *      replace:integer\(\$query, method\('$1'\),
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
            ->allowEmptyString('id', 'create')
            ->allowEmptyString('title')
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
                    ->where(['id' => $entity->id, 'user_id' => $this->contextUser()->artistId()])
                    ->select(['edition_id'])->toArray();
            $entity->edition_id = $query[0]->edition_id;
        }
        $this->clearCache($entity->edition_id);
        osdLog($entity, 'afterSave on this format entity');
    }

    /**
     * beforeFind event
     *
     * @param Event $event
     * @param Query $query
     * @param ArrayObject $options
     * @param boolean $primary
     */
    public function beforeFind($event, $query, $options, $primary) {
//        $this->includeArtistIdCondition($query); // trait handles this
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

    /**
     * Find formats by id
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findFormats($query, $options) {
        return $this->integer($query, 'id', $options['values']);
    }

    /**
     * Find formats where all assigned pieces are Transferred
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findSoldOut($query, $options = []) {
        return $query->find('collectedPieceCount', ['value' => ['>', 0]])
            ->where(['collected_piece_count' => 'assigned_piece_count']);
    }

    /**
     * Find formats with a number or range of Transferred pieces
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findCollectedPieceCount($query, $options) {
        return $this->integer($query, 'collected_piece_count', $options['values']);
    }

    /**
     * Find formats with a number or range percentage of Transferred pieces
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findCollectedPiecePercentage($query, $options) {
        return $this->integer($query,
            'collected_piece_count / assigned_piece_count',
            $options['values']);
    }

    /**
     * Find formats in a subscription (or set of subscriptions)
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function FindInSubscriptions($query, $options) {
        return $this->integer($query, 'subscription_id', $options['values']);
    }

    /**
     * Find formats in a edition (or set of editions)
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function FindInEditions($query, $options) {
        return $this->integer($query, 'edition_id', $options['values']);
    }

    /**
     * Find formats linked to an image (or set of images)
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findHasImage($query, $options) {
        return $this->integer($query, 'image_id', $options['values']);
    }

    /**
     * Find formats that have fluid pieces (un-disposed)
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findHasFluid($query, $options = []) {
        return $query->where(['fluid_piece_count >' => 0]);
    }

    /**
     * Find formats that have no assigned pieces
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findEmpty($query, $options = []) {
        return $query->where(['assigned_piece_count' => 0]);
    }

    /**
     * Find formats with a title matching or containing a string
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findTitle($query, $options = []) {
        return $this->string($query, 'title', $options[$value]);
    }

    /**
     * Find formats with a description matching or containing a string
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findDescription($query, $options = []) {
        return $this->string($query, 'title', $options[$value]);
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

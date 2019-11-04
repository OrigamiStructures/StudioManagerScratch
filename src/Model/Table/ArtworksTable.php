<?php
namespace App\Model\Table;

use App\Model\Entity\Artwork;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use ArrayObject;
use App\Model\Lib\ArtistIdConditionTrait;
use App\Model\Behavior\StringQueryBehavior;
use App\Model\Behavior\IntegerQueryBehavior;

/**
 * Artworks Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Images
 * @property \Cake\ORM\Association\HasMany $Editions
 */
class ArtworksTable extends AppTable
{
    use ArtistIdConditionTrait;

// <editor-fold defaultstate="collapsed" desc="Core methods">

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)     {
        parent::initialize($config);
        $this->_initializeProperties();
        $this->_initializeBehaviors();
        $this->_initializeAssociations();
    }


// <editor-fold defaultstate="collapsed" desc="Initialization methods">
    protected function _initializeProperties() {
        $this->setTable('artworks');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
    }

        protected function _initializeBehaviors() {
        $this->addBehavior('Timestamp');
        $this->addBehavior('Family');
        $this->addBehavior('ArtworkStack');
        $this->addBehavior('IntegerQuery');
        $this->addBehavior('StringQuery');
    }

        protected function _initializeAssociations() {
        $this->belongsTo('Users',
                        ['foreignKey' => 'user_id',]);
        $this->belongsTo('Images',
                        ['foreignKey' => 'image_id',]);
        $this->hasMany('Editions',
                        ['foreignKey' => 'artwork_id',]);
    }

// </editor-fold>

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)     {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', 'create');

        $validator->allowEmptyString('title');

        $validator->allowEmptyString('description');

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
        $rules->add($rules->existsIn(['image_id'], 'Images'));
        return $rules;
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Lifecycle methods">

    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options) {
        $this->initImages($data);
        $this->initIDs($data); // add in user_ids
        $this->initPieces($data);
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

// <editor-fold defaultstate="collapsed" desc="Custom Finders">

    /**
     * Find artworks by id
     *
     * @todo is this where the stack cache storage/retrieval will be done?
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findArtworks($query, $options) {
        return $this->integer($query, 'id', $options['values']);
    }

    /**
     * Find artworks with a title matching or containing a string
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findTitle($query, $options = []) {
        return $this->string($query, 'title', $options[$value]);
    }

    /**
     * Find artworks with a description matching or containing a string
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findDescription($query, $options = []) {
        return $this->string($query, 'title', $options[$value]);
    }

    /**
     * Find artworks linked to an image (or set of images)
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findHasImage($query, $options) {
        return $this->integer($query, 'image_id', $options['values']);
    }

    /**
     * Find formats with a number or range of Transferred pieces
     *
     * @param Query $query
     * @param array $options see IntegerQueryBehavior
     * @return Query
     */
    public function findEditionCount($query, $options) {
        return $this->integer($query, 'edition_count', $options['values']);
    }

    /**
     * @todo no known use
     *
     * @param Query $query
     * @param type $options
     * @return Query
     */
    public function findWork(Query $query, $options) {
        return $query
                ->where(['user_id' => $options['artist_id'], 'id' => $options['artwork_id']])
                ->contain(['Editions' => ['Pieces', 'Series', 'Formats' => ['Pieces', 'Subscriptions']]]);
    }

    public function findSearch(Query $query, $options) {
        $query->where([
                'Artworks.title LIKE' => "%{$options[0]}%",
                'Artworks.user_id' => $this->contextUser()->artistId()
            ])
            // CONTAINMENT IS FROM THE COMPONENT.
            // ABSTRACT THIS
            ->contain([
                'Users', 'Images', /* 'Editions.Users', */ 'Editions' => [
                    'Series', 'Pieces', /* 'Formats.Users', */ 'Formats' => [
                        'Images', 'Pieces' => ['Dispositions'], /* 'Subscriptions' */
                    ]
                ]
        ]);
        return $query->toArray();
    }

// </editor-fold>

}

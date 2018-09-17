<?php
namespace App\Model\Table;

use App\Model\Entity\Artwork;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use ArrayObject;

/**
 * Artworks Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Images
 * @property \Cake\ORM\Association\HasMany $Editions
 */
class ArtworksTable extends AppTable
{
	
//    public function implementedEvents()
//    {
//		$events = [
//            'Model.beforeMarshal' => 'beforeMarshal',
//        ];
//		return array_merge(parent::implementedEvents(), $events);
//    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('artworks');
        $this->displayField('title');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
		$this->addBehavior('Family');
		$this->addBehavior('ArtworkStack');

		$this->belongsTo('Users', [
			'foreignKey' => 'user_id',
		]);
        $this->belongsTo('Images', [
            'foreignKey' => 'image_id',
        ]);
        $this->hasMany('Editions', [
            'foreignKey' => 'artwork_id',
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
	
	/**
	 * 
	 * @param Query $query
	 * @param type $options
	 * @return Query
	 */
	public function findFocusedWork(Query $query, $options) {
		
		return $query
			->where(['user_id' => $options['artist_id'], 'id' => $options['artwork_id']])
			->contain(['Editions' => ['Pieces', 'Series', 'Formats' => ['Pieces', 'Subscriptions']]]);
	}
	
	/**
	 * 
	 * @param Query $query
	 * @param type $options
	 * @return Query
	 */
	public function findWorks(Query $query, $options) {
		
	}
	
	public function findClientWorks(Query $query, $options) {
		
	}
	
	
	
	public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options) {
		$this->initImages($data);
		$this->initIDs($data); // add in user_ids
		$this->initPieces($data);
	}
	
	public function findSearch(Query $query, $options) {
		$query->where([
			'Artworks.title LIKE' => "%{$options[0]}%",
			'Artworks.user_id' => $this->SystemState->artistId()
			])
		// CONTAINMENT IS FROM THE COMPONENT.
		// ABSTRACT THIS
				->contain([
		'Users', 'Images', /*'Editions.Users',*/ 'Editions' => [
			'Series', 'Pieces', /*'Formats.Users',*/ 'Formats' => [
				'Images', 'Pieces' => ['Dispositions'], /*'Subscriptions'*/
				]
			]
		]);
//				->where(['Artworks.user_id' => $this->SystemState->artistId()])
//				->cache('artwork');
//		sql($query);
		return $query->toArray();
	}
	
	/**
	 * @todo Considering a refactor of ArtStackComponent
	 * This could make it a regular finder query compatible with pagination
	 * 
	 * @param Query $query
	 * @param type $options
	 */
	public function findArtStack(Query $query, $options){
		
	}
	
}

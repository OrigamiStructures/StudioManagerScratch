<?php
namespace App\Model\Table;

use App\Model\Entity\Edition;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Model\Behavior\FamilyBehavior;
use Cake\ORM\TableRegistry;
use App\Lib\SystemState;

/**
 * Editions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Artworks
 * @property \Cake\ORM\Association\BelongsTo $Series
 * @property \Cake\ORM\Association\HasMany $Formats
 * @property \Cake\ORM\Association\HasMany $Pieces
 */
class EditionsTable extends AppTable
{
	/**
	 * The allowable types of editions
	 * 
	 * @var array
	 */
	protected $types = [
		'Edition' => [
			EDITION_LIMITED => 'Limited (numbered)',
			EDITION_OPEN => 'Open (un-numbered)',			
		],
		'Portfolio' => [
			PORTFOLIO_LIMITED => 'Limited (numbered)',
			PORTFOLIO_OPEN => 'Open (un-numbered)',
		],
		'Publication' => [
			PUBLICATION_LIMITED => 'Limited (numbered)',
			PUBLICATION_OPEN => 'Open (un-numbered)',
		],
		EDITION_UNIQUE => 'Unique Work',
		EDITION_RIGHTS => 'Rights',
	];


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
		$this->addBehavior('Family');
		$this->addBehavior('ArtworkStack');
		$this->addBehavior('CounterCache', [
            'Artworks' => ['edition_count']
        ]);

//		if (!isset($this->SystemState) || $this->SystemState->is(ARTWORK_SAVE)) {
//		if ($this->SystemState->is(ARTWORK_SAVE)) {
		$this->belongsTo('Users',
				[
			'foreignKey' => 'user_id',
		]);
		$this->belongsTo('Artworks',
				[
			'foreignKey' => 'artwork_id',
		]);
//		}		
        $this->belongsTo('Series', [
            'foreignKey' => 'series_id',
        ]);
        $this->hasMany('Formats', [
            'foreignKey' => 'edition_id',
        ]);
        $this->hasMany('Pieces', [
            'foreignKey' => 'edition_id',
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
        $rules->add($rules->existsIn(['series_id'], 'Series'));
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
	
	public function choiceList($id, $index_name, $options = []) {
//		$this->displayField('displayTitle');
		$options += ['id' => $id, 'index_name' => $index_name, 'valueField' => 'displayTitle'];
//		osd($options, 'options');die;
		return $this->find('memberList', $options)->toArray();
	}

		/**
	 * 
	 * @return array
	 */
	public function typeList() {
		return $this->types;
	}
	
	/**
	 * Get the minimum alowed size for an edition
	 * 
	 * Given an edition entity or the ID of an edition, return the piece number 
	 * which is the highest numbered disposed piece in the edition or, if it's 
	 * an open edition, the number of disposed pieces in the edition. 
	 * This number will represent the minimum size for the edition.
	 * 
	 * @param integer|Entity $edition
	 * @return integer The minimum size
	 */
	public function minimumSize($edition) {
		if (is_int($edition)) {
			$edition = $this->get($edition, ['conditions' => [
				'user_id' => $this->SystemState->artistId(),
			]]);
		}
		
		if (!($edition instanceof Edition)) {
			throw new \BadMethodCallException('An Edition entity or an ID that '
					. 'could lead to an Edition entity was required.');
		}		
		
		if (in_array($edition->type, SystemState::limitedEditionTypes())) {
			/**
			 * Limited editions nip undisposed pieces from the end of the edition
			 */
			$minimum = $this->Pieces->highestNumberDisposed(['edition_id' => $edition->id]);

		} else {

			/**
			 * Open edititions can delete any undisposed pieces
			 */
			$minimum = $edition->disposed_piece_count/* > 0 ? $edition->disposed_piece_count : 1 */;
		}
		
		return $minimum;
	
	}
	
}

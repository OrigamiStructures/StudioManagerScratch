<?php
namespace App\Model\Table;

use App\Model\Entity\Edition;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Model\Behavior\FamilyBehavior;

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
		1 => 'Unique', 'Limited Edition', 'Open Edition', 'Use', 'Portfolio'
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
		return array_combine($this->types, $this->types);
	}
	
}

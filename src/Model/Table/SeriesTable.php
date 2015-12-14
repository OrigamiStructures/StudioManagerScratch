<?php
namespace App\Model\Table;

use App\Model\Entity\Series;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Collection\Collection;

/**
 * Series Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\HasMany $Editions
 */
class SeriesTable extends Table
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

        $this->table('series');
        $this->displayField('title');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Editions', [
            'foreignKey' => 'series_id'
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
	
	public function findUnimplemented(Query $query, $options) {
		$options += ['artwork_id' => '', 'artist_id' => ''];
		$editions = $this->Editions->find('list', [
			'keyField' => 'series_id', 'valueField' => 'artwork_id'
		])
			->where(['artwork_id' => $options['artwork_id'], 'series_id >=' => '0'])
			->toArray();
		$all_series = new Collection($this->find('choiceList', $options));
		$implemented = array_keys($editions);
		$series = $all_series->filter(function($series_title, $series_id) use ($implemented) {
			return !in_array($series_id, $implemented);
		});
		osd($series->toArray());
		osd($options, 'options');
		osd($editions);
		osd($series->toArray());die;
	}
	
}

<?php
namespace App\Model\Table;

use App\Model\Entity\Series;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Collection\Collection;
use App\Lib\SystemState;

/**
 * Series Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\HasMany $Editions
 */
class SeriesTable extends AppTable
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
		$this->addBehavior('Family');

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
	
	public function choiceList($options) {
//		debug(\Cake\Error\Debugger::trace());die;
		if ($this->SystemState->is(ARTWORK_CREATE)) {
			return $this->find('unimplemented', $options);
		} else {
			return $this->find('choiceList', $options);
		}
	}

		/**
	 * Get the full series list for an artist
	 * 
	 * For a select input, id => title
	 * 
	 * @param Query $query
	 * @param string $artist_id
	 * @return query result-object 
	 */
	public function findChoiceList(Query $query, $options) {
		return $query->where(['Series.user_id' => $options['artist_id']])->find('list');
	}
	
	/**
	 * Get the unimplemented series list for an artist's single artwork
	 * 
	 * For a select input, id => title
	 * 
	 * @param Query $query
	 * @param array $options
	 * @return query result-object 
	 */
	public function findUnimplemented(Query $query, $options) {
		$options += ['artwork_id' => '', 'artist_id' => ''];
		
		$query = $this->find('choiceList', $options)
			->notMatching('Editions', function($q) use ($options) {
				return $q->where(['artwork_id' => $options['artwork_id'], 'series_id >=' => '0']);
			});
		return $query;
	}
	
}

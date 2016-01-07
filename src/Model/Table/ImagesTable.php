<?php
namespace App\Model\Table;

use App\Model\Entity\Image;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Images Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\HasMany $Artworks
 * @property \Cake\ORM\Association\HasMany $Formats
 * @property \Cake\ORM\Association\HasMany $Members
 */
class ImagesTable extends AppTable
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

        $this->table('images');
        $this->displayField('title');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

		if ($this->SystemState->is(ARTWORK_SAVE)) {
			$this->belongsTo('Users', [
				'foreignKey' => 'user_id'
			]);
			$this->hasMany('Artworks', [
				'foreignKey' => 'image_id'
			]);
			$this->hasMany('Formats', [
				'foreignKey' => 'image_id'
			]);
			$this->hasMany('Members', [
				'foreignKey' => 'image_id'
			]);
		}		
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
            ->allowEmpty('image');

        $validator
            ->allowEmpty('image_dir');

        $validator
            ->allowEmpty('mimetype');

        $validator
            ->allowEmpty('filesize');

        $validator
            ->add('width', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('width');

        $validator
            ->add('height', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('height');

        $validator
            ->allowEmpty('title');

        $validator
            ->allowEmpty('date');

        $validator
            ->allowEmpty('alt');

        $validator
            ->add('upload', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('upload');

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
}

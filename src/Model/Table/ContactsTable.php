<?php
namespace App\Model\Table;

use App\Model\Entity\Contact;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use ArrayObject;

/**
 * Contacts Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Members
 */
class ContactsTable extends AppTable
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

        $this->table('contacts');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Members', [
            'foreignKey' => 'member_id',
            'joinType' => 'INNER'
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
            ->allowEmpty('label');

        $validator
            ->allowEmpty('data');

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
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        return $rules;
    }
        
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

	/**
	 * Make the specified number of new Contact arrays (for TRD use)
	 * 
	 * @param integer $count How many contacts are needed
	 * @param array $default [column => value] to control what data the pieces have
	 * @param integer $start The index (and number) of the first of the ($count) pieces
	 */
	public function spawn($count, $default = [], $start = 0) {
		$columns = $default + [
			'id' => NULL,
			'user_id' => $this->SystemState->artistId(),
            'label' => 'New'
		];
        
        return array_fill($start, $count, $columns);
	}
	
}

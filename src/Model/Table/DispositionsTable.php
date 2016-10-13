<?php
namespace App\Model\Table;

use App\Model\Entity\Disposition;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use ArrayObject;
use Cake\ORM\TableRegistry;
use Cake\Collection\Collection;

/**
 * Dispositions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Members
 * @property \Cake\ORM\Association\BelongsTo $Addresses
 * @property \Cake\ORM\Association\BelongsTo $Dispositions
 * @property \Cake\ORM\Association\HasMany $Dispositions
 * @property \Cake\ORM\Association\BelongsToMany $Pieces
 */
class DispositionsTable extends AppTable
{

    /**
	 * Map specific disposition labels to their underlying types
	 * 
	 * Should this be in the Table class?
	 * 
	 * @var array
	 */
	protected $_map = [
		DISPOSITION_TRANSFER_SALE		=> DISPOSITION_TRANSFER,	
		DISPOSITION_TRANSFER_SUBSCRIPTION => DISPOSITION_TRANSFER,	
		DISPOSITION_TRANSFER_DONATION	=> DISPOSITION_TRANSFER,	
		DISPOSITION_TRANSFER_GIFT		=> DISPOSITION_TRANSFER,
		DISPOSITION_TRANSFER_RIGHTS		=> DISPOSITION_TRANSFER,
		
		DISPOSITION_LOAN_SHOW			=> DISPOSITION_LOAN,
		DISPOSITION_LOAN_CONSIGNMENT	=> DISPOSITION_LOAN,
		DISPOSITION_LOAN_PRIVATE		=> DISPOSITION_LOAN,	
		DISPOSITION_LOAN_RENTAL			=> DISPOSITION_LOAN,
		DISPOSITION_LOAN_RIGHTS			=> DISPOSITION_LOAN,
		
		DISPOSITION_STORE_STORAGE		=> DISPOSITION_STORE,

		DISPOSITION_UNAVAILABLE_LOST	=> DISPOSITION_UNAVAILABLE,
		DISPOSITION_UNAVAILABLE_DAMAGED => DISPOSITION_UNAVAILABLE,
		DISPOSITION_UNAVAILABLE_STOLEN  => DISPOSITION_UNAVAILABLE,
	];
	
	public $disposition_label = [
		'Transfer ownership' =>
		[DISPOSITION_TRANSFER_SALE		=> DISPOSITION_TRANSFER_SALE,	
		DISPOSITION_TRANSFER_SUBSCRIPTION => DISPOSITION_TRANSFER_SALE,	
		DISPOSITION_TRANSFER_DONATION	=> DISPOSITION_TRANSFER_DONATION,	
		DISPOSITION_TRANSFER_GIFT		=> DISPOSITION_TRANSFER_GIFT,
		DISPOSITION_TRANSFER_RIGHTS		=> DISPOSITION_TRANSFER_RIGHTS,],
		
		'Temporary placement' => 
		[DISPOSITION_LOAN_SHOW			=> DISPOSITION_LOAN_SHOW,
		DISPOSITION_LOAN_CONSIGNMENT	=> DISPOSITION_LOAN_CONSIGNMENT,
		DISPOSITION_LOAN_PRIVATE		=> DISPOSITION_LOAN_PRIVATE,	
		DISPOSITION_LOAN_RENTAL			=> DISPOSITION_LOAN_RENTAL,
		DISPOSITION_LOAN_RIGHTS			=> DISPOSITION_LOAN_RIGHTS,],
		
		'Storage' => 
		[DISPOSITION_STORE_STORAGE		=> DISPOSITION_STORE_STORAGE,],

		'Out of circulation' => 
		[DISPOSITION_UNAVAILABLE_LOST	=> DISPOSITION_UNAVAILABLE_LOST,
		DISPOSITION_UNAVAILABLE_DAMAGED => DISPOSITION_UNAVAILABLE_DAMAGED,
		DISPOSITION_UNAVAILABLE_STOLEN  => DISPOSITION_UNAVAILABLE_STOLEN,],
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

        $this->table('dispositions');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
		$this->addBehavior('CounterCache', [
			/**
			 * Disposition count > 0 prevents Pieces from being assigned to 
			 * new Formats. At some point we could have a second disp_count 
			 * that didn't prevent this. These would be pieces in some 
			 * internal process disposition that didn't lock determine 
			 * their physical nature (some planning or working stage?)
			 * In this case we could just do a smart disposition_count 
			 * instead of doing two count fields.
			 */
//            'Pieces' => [
//				'disposition_count',
//				'collected' => [$this, 'markCollected'],
//				/*'internal_dispo_count'*/],
			'Members' => [
				'disposition_count',
				'collector' => [$this, 'markCollected']
			]
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Members', [
            'foreignKey' => 'member_id'
        ]);
        $this->belongsTo('Addresses', [
            'foreignKey' => 'address_id'
        ]);
//        $this->belongsTo('OwnerDisposition', [
//            'foreignKey' => 'disposition_id'
//        ]);
        $this->hasMany('Dispositions', [
            'foreignKey' => 'disposition_id'
        ]);
        $this->belongsToMany('Pieces', [
            'foreignKey' => 'disposition_id',
            'targetForeignKey' => 'piece_id',
            'joinTable' => 'dispositions_pieces'
        ]);
    }

	public function map($label) {
		if (isset($this->_map[$label])) {
			return $this->_map[$label];
		} else {
			return FALSE;
		}
		
	}
	
	/**
	 * Is the label a Rights type of disposition
	 * 
	 * @param string $label
	 * @return boolean
	 */
	public function isRights($label) {
		return in_array($label, [DISPOSITION_TRANSFER_RIGHTS, DISPOSITION_LOAN_RIGHTS]);
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
            ->allowEmpty('id', 'create')
			->requirePresence('start_date');
        $validator
			->add('label', 'valid_label', [
				'rule' => [$this, 'validLabel'],
				'message' => 'The disposition must be chosen from the provided list',
			])
            ->notEmpty('label');
        $validator
			->add('end_date', 'end_of_loan', [
				'rule' => [$this, 'endOfLoan'], 
				'message' => 'Loans are for a limited time. Please provide an end date greater than the start date.'
				])
			->requirePresence('end_date');

        return $validator;
    }

	public function validLabel ($value, $context) {
		return array_key_exists($value, $this->_map);
	}

	public function endOfLoan($data, $context) {
		$data = $context['data'];
		if (!isset($data['start_date'])) {
			return FALSE;
		} elseif (is_object($data['start_date'])) {
			// already took care of this stuff at 'create' 
			return TRUE;
		}

		$start = implode('', $data['start_date']);
		$end = is_array($data['end_date']) ? implode('', $data['end_date']) : 0 ;
		if ($data['type'] !== DISPOSITION_LOAN) {
			$result = TRUE;
		} else {
			$result = $start < $end;
		}
		return $result;
	}

	/**
	 * Lookup and set the disposition type
	 * 
	 * @param Event $event
	 * @param ArrayObject $data
	 * @param ArrayObject $options
	 */
	public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options) {
		if (isset($data['label']) && array_key_exists($data['label'], $this->_map)) {
			$data['type'] = $this->_map[$data['label']];
		}
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
        $rules->add($rules->existsIn(['disposition_id'], 'Dispositions'));
        $rules->add($rules->existsIn(['piece_id'], 'Pieces'));
        return $rules;
    }
	
	public function markCollected($event, $entity, $table) {
		$conditions = [
			'type' => DISPOSITION_TRANSFER,
			'member_id' => $entity->member_id
		];
		$members = $table->find()->where($conditions);
		return $members->count();
	}
	
	/**
	 * Set counter cache fields on pieces
	 * 
	 * Because of the kind of association, CounterCache doesn't handle pieces 
	 * so I put this in to take care of those counts
	 * 
	 * @param Event $event
	 * @param type $entity
	 */
    public function afterSave(Event $event, $entity)
    {
		$table = \Cake\ORM\TableRegistry::get('Pieces');
		foreach ($entity->pieces as $piece) {
			$status_events = $this->DispositionsPieces
				->find()
				->where(['piece_id' => $piece->id])
				->contain('Dispositions');
			$events = new Collection($status_events);
			$counts = $events->reduce(function($accum, $event){
				$accum['collected'] += $event->disposition->type === DISPOSITION_TRANSFER;
				$accum['disposition_count']++;// += $event->disposition->type !== DISPOSITION_TRANSFER;
				return $accum;
			}, ['collected' => 0, 'disposition_count' => 0]);
			$piece = $table->patchEntity($piece, $counts);
			$table->save($piece);
		}
    }
	
	/**
	 * Do counter cache reductions for pieces
	 * 
	 * Because of the kind of association, CounterCache doesn't handle pieces 
	 * so I put this in to take care of those counts
	 * 
	 * @param Event $event
	 * @param type $entity
	 */
    public function afterDelete(Event $event, $entity)
    {
		$this->afterSave($event, $entity);
    }

}

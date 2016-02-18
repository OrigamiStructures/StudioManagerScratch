<?php
namespace App\Model\Table;

use App\Model\Entity\Disposition;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Dispositions Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Members
 * @property \Cake\ORM\Association\BelongsTo $Locations
 * @property \Cake\ORM\Association\BelongsTo $Pieces
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
		DISPOSITION_TRANSFER_SALE		=> DISPOSITION_TRANSFER_SALE,	
		DISPOSITION_TRANSFER_DONATION	=> DISPOSITION_TRANSFER_DONATION,	
		DISPOSITION_TRANSFER_GIFT		=> DISPOSITION_TRANSFER_GIFT,
		DISPOSITION_TRANSFER_RIGHTS		=> DISPOSITION_TRANSFER_RIGHTS,
		
		DISPOSITION_LOAN_SHOW			=> DISPOSITION_LOAN_SHOW,
		DISPOSITION_LOAN_CONSIGNMENT	=> DISPOSITION_LOAN_CONSIGNMENT,
		DISPOSITION_LOAN_PRIVATE		=> DISPOSITION_LOAN_PRIVATE,	
		DISPOSITION_LOAN_RENTAL			=> DISPOSITION_LOAN_RENTAL,
		DISPOSITION_LOAN_RIGHTS			=> DISPOSITION_LOAN_RIGHTS,
		
		DISPOSITION_STORE_STORAGE		=> DISPOSITION_STORE_STORAGE,

		DISPOSITION_UNAVAILABLE_LOST	=> DISPOSITION_UNAVAILABLE_LOST,
		DISPOSITION_UNAVAILABLE_DAMAGED => DISPOSITION_UNAVAILABLE_DAMAGED,
		DISPOSITION_UNAVAILABLE_STOLEN  => DISPOSITION_UNAVAILABLE_STOLEN,
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
            'Pieces' => [
				'disposition_count',
				'collected' => [$this, 'markCollected'],
				/*'internal_dispo_count'*/]
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
        $this->belongsToMany('Pieces', [
            'foreignKey' => 'disposition_id',
            'targetForeignKey' => 'piece_id',
            'joinTable' => 'dispositions_pieces',
        ]);
    }
	
	public function map($label) {
		return $this->_map[$label];
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
        $rules->add($rules->existsIn(['location_id'], 'Locations'));
        $rules->add($rules->existsIn(['piece_id'], 'Pieces'));
        return $rules;
    }
	
	public function markCollected($event, $entity, $table) {
		// this search must find dispositions that would be considered 'collected'
		return (boolean) 0;
	}
}

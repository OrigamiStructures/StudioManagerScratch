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
use \SPLStorageObject;
use Cake\I18n\Time;
use App\Model\Behavior\DateQueryBehavior;
use App\Model\Lib\ArtistIdConditionTrait;

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
class DispositionsTable extends AppTable {

    use ArtistIdConditionTrait;

// <editor-fold defaultstate="collapsed" desc="Type and Label mapping properties">

    /**
     * Map specific disposition labels to their underlying types
     * 
     * @var array
     */
    protected $_map = [
        DISPOSITION_TRANSFER_SALE => DISPOSITION_TRANSFER,
        DISPOSITION_TRANSFER_SUBSCRIPTION => DISPOSITION_TRANSFER,
        DISPOSITION_TRANSFER_DONATION => DISPOSITION_TRANSFER,
        DISPOSITION_TRANSFER_GIFT => DISPOSITION_TRANSFER,
        DISPOSITION_TRANSFER_RIGHTS => DISPOSITION_TRANSFER,
        DISPOSITION_LOAN_SHOW => DISPOSITION_LOAN,
        DISPOSITION_LOAN_CONSIGNMENT => DISPOSITION_LOAN,
        DISPOSITION_LOAN_PRIVATE => DISPOSITION_LOAN,
        DISPOSITION_LOAN_RENTAL => DISPOSITION_LOAN,
        DISPOSITION_LOAN_RIGHTS => DISPOSITION_LOAN,
        DISPOSITION_STORE_STORAGE => DISPOSITION_STORE,
        DISPOSITION_UNAVAILABLE_LOST => DISPOSITION_UNAVAILABLE,
        DISPOSITION_UNAVAILABLE_DAMAGED => DISPOSITION_UNAVAILABLE,
        DISPOSITION_UNAVAILABLE_STOLEN => DISPOSITION_UNAVAILABLE,
        DISPOSITION_NFS => DISPOSITION_UNAVAILABLE,
    ];

    /**
     * The list of valid types
     * 
     * @var array
     */
    protected $_disposition_type = [
        DISPOSITION_TRANSFER,
        DISPOSITION_LOAN,
        DISPOSITION_STORE,
        DISPOSITION_UNAVAILABLE,
    ];

    /**
     * List of valid labels grouped by type
     *
     * @var array
     */
    protected $_disposition_label = [
        'Transfer ownership' =>
        [DISPOSITION_TRANSFER_SALE => DISPOSITION_TRANSFER_SALE,
            DISPOSITION_TRANSFER_SUBSCRIPTION => DISPOSITION_TRANSFER_SUBSCRIPTION,
            DISPOSITION_TRANSFER_DONATION => DISPOSITION_TRANSFER_DONATION,
            DISPOSITION_TRANSFER_GIFT => DISPOSITION_TRANSFER_GIFT,
            DISPOSITION_TRANSFER_RIGHTS => DISPOSITION_TRANSFER_RIGHTS,],
        'Temporary placement' =>
        [DISPOSITION_LOAN_SHOW => DISPOSITION_LOAN_SHOW,
            DISPOSITION_LOAN_CONSIGNMENT => DISPOSITION_LOAN_CONSIGNMENT,
            DISPOSITION_LOAN_PRIVATE => DISPOSITION_LOAN_PRIVATE,
            DISPOSITION_LOAN_RENTAL => DISPOSITION_LOAN_RENTAL,
            DISPOSITION_LOAN_RIGHTS => DISPOSITION_LOAN_RIGHTS,],
        'Storage' =>
        [DISPOSITION_STORE_STORAGE => DISPOSITION_STORE_STORAGE,],
        'Out of circulation' =>
        [DISPOSITION_UNAVAILABLE_LOST => DISPOSITION_UNAVAILABLE_LOST,
            DISPOSITION_UNAVAILABLE_DAMAGED => DISPOSITION_UNAVAILABLE_DAMAGED,
            DISPOSITION_UNAVAILABLE_STOLEN => DISPOSITION_UNAVAILABLE_STOLEN,
            DISPOSITION_NFS => DISPOSITION_UNAVAILABLE,],
    ]; // </editor-fold>

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('dispositions');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('StartDateQuery', [
            'className' => 'DateQuery',
            'field' => 'start_date',
            'model' => $this->alias(),
            'primary_input' => 'first_start_date',
            'secondary_input' => 'second_start_date']);
        $this->addBehavior('EndDateQuery', [
            'className' => 'DateQuery',
            'field' => 'end_date',
            'model' => $this->alias(),
            'primary_input' => 'first_end_date',
            'secondary_input' => 'second_end_date']);
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
		/*'internal_dispo_count'*/],
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

    public function containAncestry($query) {
        return $query->contain(['Pieces' => ['fields' => ['id', 'DispositionsPieces.disposition_id']]]);
    }

// <editor-fold defaultstate="collapsed" desc="Destined for Entity">

    /**
     * Is the label a Rights type of disposition
     * 
     * @param string $label
     * @return boolean
     */
    public function isRights($label) {
        return in_array($label, [DISPOSITION_TRANSFER_RIGHTS, DISPOSITION_LOAN_RIGHTS]);
    }

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Validation and Rules">

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator) {
        $validator
                ->add('id', 'valid', ['rule' => 'numeric'])
                ->allowEmpty('id', 'create')
                ->requirePresence('start_date');
        $validator
                ->add('label', 'valid_label',
                        [
                            'rule' => [$this, 'validLabel'],
                            'message' => 'The disposition must be chosen from the provided list',
                ])
                ->notEmpty('label');
        $validator
                ->add('end_date', 'end_of_loan',
                        [
                            'rule' => [$this, 'endOfLoan'],
                            'message' => 'Loans are for a limited time. Please provide an end date greater than the start date.'
                ])
                ->requirePresence('end_date');

        return $validator;
    }

    /**
     * Callable for 'label' validator rule
     * 
     * @param array $value
     * @param array $context
     * @return boolean
     */
    public function validLabel($value, $context) {
        return array_key_exists($value, $this->_map);
    }

    /**
     * Callable for `end_date`, `start_date` validator rule
     * 
     * @param array $data
     * @param array $context
     * @return boolean
     */
    public function endOfLoan($data, $context) {
        $data = $context['data'];
        if (!isset($data['start_date'])) {
            return FALSE;
        } elseif (is_object($data['start_date'])) {
            // already took care of this stuff at 'create' 
            return TRUE;
        }

        $start = implode('', $data['start_date']);
        $end = is_array($data['end_date']) ? implode('', $data['end_date']) : 0;
        if ($data['type'] !== DISPOSITION_LOAN) {
            $result = TRUE;
        } else {
            $result = $start < $end;
        }
        return $result;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules) {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        $rules->add($rules->existsIn(['disposition_id'], 'Dispositions'));
        $rules->add($rules->existsIn(['piece_id'], 'Pieces'));
        return $rules;
    }

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="CounterCache callables (see events too)">

    /**
     * Callable to support MembersTable counter cache behavior
     * 
     * @param Event $event
     * @param Entity $entity
     * @param Table $table
     * @return int
     */
    public function markCollected($event, $entity, $table) {
        $conditions = [
            'type' => DISPOSITION_TRANSFER,
            'member_id' => $entity->member_id
        ];
        $members = $table->find()->where($conditions);
        return $members->count();
    }

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="LifeCylce events">

    /**
     * afterSave
     * 
     * Set counter cache fields on pieces. Because of the  HABTM 
     * association, CounterCache doesn't handle pieces so I put this in 
     * to take care of those counts
     * 
     * @param Event $event
     * @param Entity $entity
     */
    public function afterSave(Event $event, $entity) {
        $table = TableRegistry::get('Pieces');
        foreach ($entity->pieces as $piece) {
            $status_events = $this->DispositionsPieces
                    ->find()
                    ->where(['piece_id' => $piece->id])
                    ->contain('Dispositions');
            $events = new Collection($status_events);
            $counts = $events->reduce(function($accum, $event) {
                $accum['collected'] += $event->disposition->type === DISPOSITION_TRANSFER;
                $accum['disposition_count'] ++; // += $event->disposition->type !== DISPOSITION_TRANSFER;
                return $accum;
            }, ['collected' => 0, 'disposition_count' => 0]);
            $piece = $table->patchEntity($piece, $counts);
            $table->save($piece);
        }
    }

    /**
     * afterDelete
     * 
     * Do counter cache reductions for pieces. Because of the HABTM 
     * association, CounterCache doesn't handle pieces so I put this in to 
     * take care of those counts
     * 
     * @param Event $event
     * @param type $entity
     */
    public function afterDelete(Event $event, $entity) {
        $this->afterSave($event, $entity);
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
        $this->includeArtistIdCondition($query); // trait handles this
    }

    /**
     * Lookup and set the disposition type
     * 
     * @param Event $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     */
    public function beforeMarshal(Event $event, ArrayObject $data,
            ArrayObject $options) {
        if (isset($data['label']) && array_key_exists($data['label'], $this->_map)) {
            $data['type'] = $this->_map[$data['label']];
        }
    }

// </editor-fold>


    /**
     * DYNAMIC FINDERS
     * 
     * dynamic finders for all the major fields
     * type, label, name, first/last name, address 1-3, city, state, zip, country
     * 
     * I'm not sure what role these play. Will there be a single call point 
     * that auto works off available parameters (to support real-time user 
     * input) or are these always hand-written in methods?
     */
    /**
     * CUSTOM FINDER METHODS
     */

    /**
     * Find the most rescent dispositions
     * 
     * @todo This depends on the most recently created disp being the current one
     * 		I'm not sure if this is true. Won't there be future (to do) dispos? 
     * 		Or might there be a way to edit dispos that might change order?
     * 		I'm pretty sure this method is wrong.
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findCurrentDisposition(Query $query, $options) {
        return $query->orderAsc('Dispositions.created')
                ->$query->first();
    }

    /**
     * Participate in general site search feature
     * 
     * @todo Method to be determined
     * 
     * @param Query $query
     * @param array $options
     */
    public function findSearch(Query $query, $options) {
        
    }

    public function containParentId($param) {
        
    }

// <editor-fold defaultstate="collapsed" desc="Tentative Set of Loan specific queries">

    /**
     * 
     * @todo Could get a param check for a user provided date
     * @param Query $query
     * @param type $options
     * @return type
     */
    public function findLoanStartsAfter(Query $query, $options) {
        return $query->find(DISPOSITION_LOAN)->find('startDateAfter');
    }

    /**
     * 
     * @todo Is this for open or closed? Both? Sorted by complete then?
     * 
     * @param Query $query
     * @param type $options
     * @return type
     */
    public function findLoanEndsBefore(Query $query, $options) {
        return $query->find(DISPOSITION_LOAN)->find('EndDateBefore');
    }

    /**
     * Find loans that are out
     * 
     * These are loans that are started and not yet ended and loans that 
     * are started and ended but not yet complete, even if there is a 
     * subsiquent loan started. In this later case, there will be multiple 
     * loans show up for the same piece(s).
     * 
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findCurrentLoans(Query $query, $options) {
        return $query->find('OpenLoan')
            ->find('StartDateBefore', [
                $this->behaviors()->get('StartDateQuery')->primary_input =>
                date('Y-M-d', time() + DAY)
            ]);
    }

    public function findLoanOverdue(Query $query, $options) {
        return $query->find('OpenLoan')
            ->find('EndDateBefore', [
                $this->behaviors()->get('EndDateQuery')->primary_input =>
                date('Y-M-d', time() + DAY)
            ]);
    }

    public function findOpenLoan(Query $query, $options) {
        return $query->find(DISPOSITION_LOAN)
            ->where(['Dispositions.complete' => 0,]);
    }

    public function findCompletedLoan(Query $query, $options) {
        return $query->find(DISPOSITION_LOAN)
            ->where(['Dispositions.complete' => 1,]);
    }

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Find `types` active or made during date ranges">
    public function findLoanDueDuring(Query $query, $options) {
        return $query->find(DISPOSITION_LOAN)
            ->where(['Dispositions.complete' => 0,])
            ->find('EndDateBetween');
    }

    public function findLoanStartedDuring(Query $query, $options) {
        return $query->find(DISPOSITION_LOAN)
            ->where(['Dispositions.complete' => 0,])
            ->find('StartDateBetween');
    }

    public function findLoanActiveDuring(Query $query, $options) {
        return $query->find(DISPOSITION_LOAN)
            ->where(['Dispositions.complete' => 0,])
            ->find('EndDateBetween', $options);
    }

    public function findTransferDuring(Query $query, $options) {
        return $query->find(DISPOSITION_TRANSFER)
            ->find('EndDateBetween', $options);
    }

    public function findStorageDuring(Query $query, $options) {
        return $query->find(DISPOSITION_STORE)
        ->find('EndDateBetween', $options);
    }

    public function findUnavailableDuring(Query $query, $options) {
        return $query->find(DISPOSITION_UNAVAILABLE)
            ->find('EndDateBetween', $options);
    }

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="TypeFinders">
    public function findLoan(Query $query, $options) {
        return $query->where(['Dispositions.type' => DISPOSITION_LOAN,]);
    }

    public function findTransfer(Query $query, $options) {
        return $query->where(['Dispositions.type' => DISPOSITION_TRANSFER,]);
    }

    /**
     * Alias for findTransfer()
     */
    public function findCollected(Query $query, $options) {
        return $query->find(DISPOSITION_TRANSFER);
    }

    public function findStorage(Query $query, $options) {
        return $query->where(['Dispositions.type' => DISPOSITION_STORE,]);
    }

    public function findUnavailable(Query $query, $options) {
        return $query->where(['Dispositions.type' => DISPOSITION_UNAVAILABLE,]);
    }

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="'Schema List' generating methods">
    /**
     * These methods return arrays that can be used by FormHelper select-type 
     * input widgits. These are schema-introspection lists rather than 
     * user data lists and can be used to create UX toos and features.
     */

    /**
     * Return a list of (almost) all the custom finders
     * 
     * This can be used to make a drop-list for the user to pick from. 
     * A multi-select could allow user defined complex finds we didn't think of.
     * ['PastLoans' => 'PastLoans', 'Open' => 'Open', ... ] 
     * 
     * @todo This will probably be useful for other Tables and could be in a Trait
     * @return array
     */
    public function customFinders() {
        $omit = ['', 'List', 'Threaded', 'OrCreate'];
        $finders = preg_filter('/find(.*)/', '$1', get_class_methods($this));
        $methods = array_diff($finders, $omit);
        $finders = array_combine($methods, $methods);
        foreach ($this->behaviors()->loaded() as $behavior) {
            $calls = array_keys($this->behaviors()->get($behavior)->implementedFinders());
            $finders += array_combine($calls, $calls);
        }
        return $finders;
    }

    /**
     * Return a list of valid labels grouped by type
     * [ typeName1 => [
     * 		labelName1 => labelName1,
     * 		labelName2 => labelName2, ]
     * 	 typeName2 => [
     * 		labelName1 => labelName1,
     * 		... ]
     * ]
     * 
     * @return array
     */
    public function labels() {
        return $this->_disposition_label;
    }

    /**
     * Return a list of valid disposition types
     * 
     * [ typeName1 => typeName2, typeName2 => typeName2 ]
     * 
     * @return array
     */
    public function types() {
        return array_combine($this->_disposition_type, $this->_disposition_type);
    }

// </editor-fold>
// 
}

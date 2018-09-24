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
//use App\Lib\Traits\EditionStackCache;

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
	
//	use EditionStackCache;

    /**
	 * Map specific disposition labels to their underlying types
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
		DISPOSITION_NFS  => DISPOSITION_UNAVAILABLE,
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
		[DISPOSITION_TRANSFER_SALE		=> DISPOSITION_TRANSFER_SALE,	
		DISPOSITION_TRANSFER_SUBSCRIPTION => DISPOSITION_TRANSFER_SUBSCRIPTION,	
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
		DISPOSITION_UNAVAILABLE_STOLEN  => DISPOSITION_UNAVAILABLE_STOLEN,
		DISPOSITION_NFS  => DISPOSITION_UNAVAILABLE,],
	];
	
	/**
	 * Track whether the artist_id has been added to the query where clause
	 * 
	 * This will let me avoid doing it again and again for complex queries. 
	 * Of course, I'm assuming this will be an issue worth solving.
	 * This will be an array that uses the object as a key and 
	 * a boolean as a value to indicate done (TRUE), not done (->contains).
	 * The TRUE value is not important. ->contains is the important test.
	 * 
	 * This property will made into an SPLStorageObject by intialize()
	 */
	protected $_where_artist_id;

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
		
		$this->_where_artist_id = new \SplObjectStorage();
		
    }

	/**
	 * After save, clear any effected edition stackQuery cache
	 * 
	 * This afterSave is not needed because the counterCache saves 
	 * upstream will get the cache (I think)
	 * 
	 * @param type $event
	 * @param type $entity
	 * @param type $options
	 */
//	public function afterSave($event, $entity, $options){
//		$this->clearCache($entity->edition_id);
//	}

	public function map($label) {
		if (isset($this->_map[$label])) {
			return $this->_map[$label];
		} else {
			return FALSE;
		}
		
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
	
	public function retrieve() {
		osd($this->behaviors());
	}
	/**
	 * CUSTOM FINDER METHODS
	 */
	
// <editor-fold defaultstate="collapsed" desc="Custom Finder support methods">
	/**
	 * Insure the artist_id is included one time
	 * 
	 * @param Query $query
	 * @return $query
	 */
	protected function _setUserId($query) {
		if (!$this->_where_artist_id->contains($query)) {
			$this->_where_artist_id->attach($query, TRUE);
			$query->where(['Dispositions.user_id' => $this->SystemState->artistId()]);
		}
		return $query;
	}

	/**
	 * Standardize and sanitize user date input
	 * 
	 * Date data will often come directly form a user input form. Turning 
	 * this input into a Time object lets us absorb a wide variety of date 
	 * input and should nuetralize any malicious or damaging input.
	 * 
	 * @todo This is probably where we want to throw an Exception. But proper 
	 *		form validation should prevent most bad input. 
	 * @param string $date 
	 * @return Time
	 */
	protected function _setDateParameter($date) {
		return new Time($date);
	}
// </editor-fold>

		/**
	 * Find the most rescent dispositions
	 * 
	 * @todo This depends on the most recently created disp being the current one
	 *		I'm not sure if this is true. Won't there be future (to do) dispos? 
	 *		Or might there be a way to edit dispos that might change order?
	 *		I'm pretty sure this method is wrong.
	 * @param Query $query
	 * @param array $options
	 * @return Query
	 */
	public function findCurrent(Query $query, $options) {
		$query = $query->orderAsc('Dispositions.created')
				->$query->first();
		return $this->_setUserId($query);
	}

	public function findOnLoan(Query $query, $options) {
		$query = $query->where(['Dispositions.type' => DISPOSITION_LOAN]);
		return $this->_setUserId($query);
	}
	
	/**
	 * 
	 * @todo Could get a param check for a user provided date
	 * @param Query $query
	 * @param type $options
	 * @return type
	 */
	public function findFutureLoans(Query $query, $options) {
		return $this->_setUserId($query)
				->find('loan')
				->find('startDateAfter', ['start_date' => time()]);
	}
 
	/**
	 * 
	 * @todo Could get a param check for a user provided date
	 * @param Query $query
	 * @param type $options
	 * @return type
	 */
	public function findPastLoans(Query $query, $options) {
		$today = $this->_setDateParameter(time());
		$query = $query->where([
			'Dispositions.type' => DISPOSITION_LOAN,
			'Dispositions.end_date <=' => $today,
					]);
		return $this->_setUserId($query);
		// uses the dispostion_id that extends a loan-type disposition
	}

	public function findOpen(Query $query, $options) {
		$query = $query->where([
			'Dispositions.type' => DISPOSITION_LOAN,
			'Dispositions.complete' => 0,
			]);
		return $this->_setUserId($query);
		// can Disposition have a method to categorize the closeness of end_date 
		//		to support display features (next_month, next_week, past_due)?
		//is completed necessary give presence of start_date and end_date? 
		//		It seems like it's one possible failure point that could be 
		//		replaced by a method complete(). Is it used by some counter_cache 
		//		in another Table/Entity?
	}
	
	public function findOverdue(Query $query, $options) {
		$today = $this->_setDateParameter(time());
		$query = $query->where([
			'Dispositions.type' => DISPOSITION_LOAN,
			'Dispositions.complete' => 0,
			'Dispositions.end_date <=' => $today, 
			]);
		return $this->_setUserId($query);
	}

// <editor-fold defaultstate="collapsed" desc="Find types active or made during date ranges">
	public function findLoanDueDuring(Query $query, $options) {
		return $query->find(DISPOSITION_LOAN)
						->where(['Dispositions.complete' => 0,])
						->find('EndDateBetween', $options);
	}

	public function findLoanStartedDuring(Query $query, $options) {
		return $query->find(DISPOSITION_LOAN)
						->where(['Dispositions.complete' => 0,])
						->find('StartDateBetween', $options);
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
	public function findLoans(Query $query, $options) {
		return $this->_setUserId($query)->where(['Dispositions.type' => DISPOSITION_LOAN,]);
	}

	public function findTransfer(Query $query, $options) {
		return $this->_setUserId($query)->where(['Dispositions.type' => DISPOSITION_TRANSFER,]);
	}

	/**
	 * Alias for findTransfer()
	 */
	public function findCollected(Query $query, $options) {
		return $query->find(DISPOSITION_TRANSFER);
	}
	
	public function findStore(Query $query, $options) {
		return $this->_setUserId($query)->where(['Dispositions.type' => DISPOSITION_STORE,]);
	}

	public function findUnavailable(Query $query, $options) {
		return $this->_setUserId($query)->where(['Dispositions.type' => DISPOSITION_UNAVAILABLE,]);
	}
// </editor-fold>

	public function findInEffectDuringDates(Query $query, $options) {
		return $this->_setUserId($query)
				->find('StartDateAfter', $options)
				->find('EndDateBefore', $options);
	}

// <editor-fold defaultstate="collapsed" desc="StartDate Finders">
	/**
	 * @todo There are two sets of date queries that are identical except for 
	 *		two string values. This suggests some kind of date-finder Trait 
	 *		that can be parameterized. The value of the current setup is 
	 *		very explicit calls. A trait would reduce the codebase quite a bit. 
	 *		An option might be a Behavior that gets parameterized so we 
	 *		could have multiple versions. This would need an override find() 
	 *		method to fold the parameters into the method names.
	 * @param Query $query
	 * @param type $options
	 * @return Query
	 */
	public function findStartDateIs(Query $query, $options) {
		$date = $this->_setDateParameter($options['start_date']);
		return $query->where(['Dispositions.start_date' => $date]);
	}

	public function findStartDateBefore(Query $query, $options) {
		$date = $this->_setDateParameter($options['start_date']);
		return $query->where(['Dispositions.start_date <' => $date]);
	}

	public function findStartDateAfter(Query $query, $options) {
		$date = $this->_setDateParameter($options['start_date']);
		return $query->where(['Dispositions.start_date >' => $date]);
	}

	public function findStartDateBetween(Query $query, $options) {
		$start_date = $this->_setDateParameter($options['start_date'])->i18nFormat('yyyy-MM-dd');
		$end_date = $this->_setDateParameter($options['end_date'])->i18nFormat('yyyy-MM-dd');
		return $this->_setUserId($query)->where(function ($exp, Query $q) use ($start_date, $end_date) {
					return $exp->between('Dispositions.start_date', 
						$start_date, $end_date);
				});
	}
// </editor-fold>
	
// <editor-fold defaultstate="collapsed" desc="EndDate Finders">
	public function findEndDateIs(Query $query, $options) {
		$date = $this->_setDateParameter($options['end_date']);
		return $query->where(['Dispositions.end_date' => $date]);
	}

	public function findEndDateBefore(Query $query, $options) {
		$date = $this->_setDateParameter($options['end_date']);
		return $query->where(['Dispositions.end_date <' => $date]);
	}

	public function findEndDateAfter(Query $query, $options) {
		$date = $this->_setDateParameter($options['end_date']);
		return $query->where(['Dispositions.end_date >' => $date]);
	}

	public function findEndDateBetween(Query $query, $options) {
		$start_date = $this->_setDateParameter($options['start_date'])->i18nFormat('yyyy-MM-dd');
		$end_date = $this->_setDateParameter($options['end_date'])->i18nFormat('yyyy-MM-dd');
		return $this->_setUserId($query)->where(function ($exp, Query $q) use ($start_date, $end_date) {
					return $exp->between('Dispositions.end_date', 
						$start_date, $end_date);
				});
	}
// </editor-fold>

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
		return array_combine($methods, $methods);
	}
	
	/**
	 * Return a list of valid labels grouped by type
	 * [ typeName1 => [
	 *		labelName1 => labelName1,
	 *		labelName2 => labelName2, ]
	 *	 typeName2 => [
	 *		labelName1 => labelName1,
	 *		... ]
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

	//dynamic finders for all the major fields
	// type, label, name, first/last name, address 1-3, city, state, zip, country

	public function findSearch(Query $query, $options) {
		
	}



}

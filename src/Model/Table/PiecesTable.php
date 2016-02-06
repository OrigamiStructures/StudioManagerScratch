<?php
namespace App\Model\Table;

use App\Model\Entity\Piece;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Collection\Collection;

/**
 * Pieces Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Editions
 * @property \Cake\ORM\Association\BelongsTo $Formats
 * @property \Cake\ORM\Association\HasMany $Dispositions
 */
class PiecesTable extends AppTable
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

        $this->table('pieces');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
		$this->addBehavior('CounterCache', [
            'Formats' => [
				'assigned_piece_count'=> [$this, 'assignedFormatPieces'],
				'fluid_piece_count'  => [$this, 'fluidFormatPieces'],
				'collected_piece_count' => ['conditions' => ['collected' => 1]],
			],
            'Editions' => [
				'assigned_piece_count' => [$this, 'assignedEditionPieces'],
				'fluid_piece_count'  => [$this, 'fluidEditionPieces'],
			]
        ]);
//		$this->addBehavior('ArtworkStack');

//		if (!isset($this->SystemState) || $this->SystemState->is(ARTWORK_SAVE)) {
//		if ($this->SystemState->is(ARTWORK_SAVE)) {
			$this->belongsTo('Users', [
				'foreignKey' => 'user_id'
			]);
			$this->belongsTo('Editions', [
				'foreignKey' => 'edition_id',
				'bindingKey' => 'id',
			]);
			$this->belongsTo('Formats', [
//				'foreignKey' => 'format_id',
				'foreignKey' => ['format_id', 'edition_id'],
				'bindingKey' => ['id', 'edition_id'],
			]);
//		}
        $this->hasMany('Dispositions', [
            'foreignKey' => 'piece_id'
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
            ->add('number', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('number');

        $validator
            ->add('quantity', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('quantity');

        $validator
            ->add('made', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('made');

        return $validator;
    }
	
	public function assignedFormatPieces($event, $entity, $table) {
		$pieces = $table->find('all')->where([
			'edition_id' => $entity->edition_id,
			'format_id' => $entity->format_id,
			]);
		return $this->assignedPieces($pieces);
	}

	public function assignedEditionPieces($event, $entity, $table) {
		$pieces = $table->find('all')->where([
			'edition_id' => $entity->edition_id,
			'format_id IS NOT NULL',
			]);
		return $this->assignedPieces($pieces);
	}

	/**
	 * Callable that calcs CounterCache Pieces that belongTo Formats
	 * 
	 * These counts are actually sums of 'quantity' on the pieces because of 
	 * the way pieces for Open Editions are tracked (avoiding making thousands 
	 * of individual records)
	 * 
	 * @param Event $event
	 * @param Entity $entity
	 * @param Table $table
	 * @return int
	 */
	public function assignedPieces(Query $query) {
		$query->select(['id', 'format_id', 'edition_id', 'quantity']);
		$sum = (new Collection($query->toArray()))->sumOf(
				function($value) {
					return $value->quantity;
				}
			);
		return $sum;
	}
	
	public function fluidFormatPieces($event, $entity, $table) {
		$pieces = $table->find('all')->where([
			'edition_id' => $entity->edition_id,
			'format_id' => $entity->format_id,
			'disposition_count' => 0,
			]);
		return $this->fluidPieces($pieces);
	}

	public function fluidEditionPieces($event, $entity, $table) {
		$pieces = $table->find('all')->where([
			'edition_id' => $entity->edition_id,
			'format_id IS NOT NULL',
			'disposition_count' => 0,
			]);
		return $this->fluidPieces($pieces);
	}

	/**
	 * Callable for CounterCache Pieces that don't have Dispositions
	 * 
	 * If a piece doesn't have a disposition, it can still be moved between 
	 * any available formats.
	 * 
	 * @param Event $event
	 * @param Entity $entity
	 * @param Table $table
	 * @return integer
	 */
	public function fluidPieces($query) {
		$query->select(['id', 'format_id', 'edition_id', 'quantity']);
		$sum = (new Collection($query->toArray()))->sumOf(
				function($value) {
					return $value->quantity;
				}
			);
		return $sum;//die;
//		}
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
//		osd(\Cake\Error\Debugger::trace());
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['edition_id'], 'Editions'));
        $rules->add($rules->existsIn(['format_id', 'edition_id'], 'Formats'));
        return $rules;
    }
	
	/**
	 * Make the specified number of new Piece arrays (for TRD use)
	 * 
	 * When new Editions are being created, new Pieces will be needed to fill 
	 * out the Artwork stack. This method makes the array nodes that, when 
	 * inserted into the form data, will generate the proper Piece records. 
	 * You can create and x-to-y record rand by passing a $start value. 
	 * Control the record data by passing $default array. 
	 * 
	 * @param boolean $numbered Numbered or un-numbered pieces (limited or open editions)
	 * @param integer $count How many pieces are needed
	 * @param array $default [column => value] to control what data the pieces have
	 * @param integer $start The index (and number) of the first of the ($count) pieces
	 */
	public function spawn($numbered, $count, $default = [], $start = 0) {
		$count += $start;
		$columns = $default + [
			'id' => NULL,
			'user_id' => $this->SystemState->artistId(),
			'number' => '',
		];
		
		$i = $start;
		while ($i < $count) {
			$pieces[$i++] = $columns;
		}

		if ($numbered) {
			$numbered_edition = (new Collection($pieces))->map(function($piece, $index){
				$piece['number'] = $index+1;
				return $piece;
			});
			$pieces = $numbered_edition->toArray();
		}
		return $pieces;
	}
	
	/**
	 * Find pieces that can gain Dispositions in this circmstance
	 * 
	 * I'm not sure if an edition id would ever be sent.
	 * 
	 * When a format_id is sent canDispose() finds:
	 *		- The already-disposed but still disposable pieces on the format
	 *		- The fluid pieces in the edition 
	 * 
	 * @param Query $query
	 * @param type $options
	 * @return type
	 */
	public function findCanDispose(Query $query, $options) {
		if (!isset($options['format_id'])) {
			throw new \BadMethodCallException("You must pass \$option['edition_id' => (integer)]");
		}
		$format_id = $options['format_id'];
		$edition_id = $this->Formats->find('parentEdition', $options)
				->select(['Editions.id'])
				->toArray()[0]['Editions']->id;
					
		if (isset($options['edition_id'])) {
			$conditions['edition_id'] = $options['edition_id'];
		}
		
//		find piece (format_id && format.disposed && piece.free) or (edition.fluid)
		return $query->where(['Pieces.format_id' => $format_id, 'Pieces.disposition_count >' => 0, /*disposable*/] )
			->orWhere(['Pieces.edition_id' => $edition_id, 'Pieces.disposition_count' => 0]);
	}
	
	/**
	 * Pieces that are not yet assigned to Formats
	 * 
	 * @param Query $query
	 * @param type $options
	 * @return type
	 * @throws \BadMethodCallException
	 */
	public function findUnassigned(Query $query, $options) {
		if (!isset($options['edition_id'])) {
			throw new \BadMethodCallException("You must pass \$option['edition_id' => (integer)]");
		}
		$query = $query->where([
			'edition_id' => $options['edition_id'],
			'format_id IS NULL',
			'user_id' => $this->SystemState->artistId(),
		]);
		return $query;
	}
	
	/**
	 * Pieces that are assigned to formats but not yet disposed
	 * 
	 * @param Query $query
	 * @param type $options
	 * @return type
	 * @throws \BadMethodCallException
	 */
	public function findFluid(Query $query, $options) {
		if (!isset($options['edition_id'])) {
			throw new \BadMethodCallException("You must pass \$option['edition_id' => (integer)]");
		}
		if (!isset($options['format_id'])) {
				$option = $options + ['format_id IS NOT NULL',
				'disposition_count' => 0,
				'user_id' => $this->SystemState->artistId(),
			];
		} else {
			$options = $options + [
				'disposition_count' => 0,
				'user_id' => $this->SystemState->artistId(),
			];
		}
		$query->where($options);
		return $query;
	}
	
	/**
	 * Pieces that are not yet disposed
	 * 
	 * @param Query $query
	 * @param type $options
	 * @return type
	 * @throws \BadMethodCallException
	 */
	public function findUndisposed(Query $query, $options) {
		if (!isset($options['edition_id'])) {
			throw new \BadMethodCallException("You must pass \$option['edition_id' => (integer)]");
		}
		$query = $query->where([
			'edition_id' => $options['edition_id'],
			'disposition_count' => 0,
			'user_id' => $this->SystemState->artistId(),
		]);
		return $query;
	}
	
	/**
	 * Pieces that are disposed
	 * 
	 * @param Query $query
	 * @param type $options
	 * @return type
	 * @throws \BadMethodCallException
	 */
	public function findDisposed(Query $query, $options) {
		if (!isset($options['edition_id'])) {
			throw new \BadMethodCallException("You must pass \$option['edition_id']");
		}
		if (isset($options['format_id'])) {
			$query->where(['format_id' => $options['format_id'],]);
		}
		$query = $query->where([
			'edition_id' => $options['edition_id'],
			'disposition_count > 0',
			'user_id' => $this->SystemState->artistId(),
		]);

		return $query;
	}
	
	/**
	 * Get the number of the highest numbered piece in an edition
	 * 
	 * THE ARGUMENTS ARE NOT IDEAL FOR THIS CALL
	 * 
	 * @param array $options conditions to find the edition
	 * @return integer
	 */
	public function highestNumberDisposed($options) {
		$disposed_pieces = $this->find('disposed', $options);
		$result = (new Collection($disposed_pieces))->max(
				function($piece) {return $piece->number;
			});
		return is_null($result) ? 0 : $result->toArray()['number'];
	}
		
	public function save(\Cake\Datasource\EntityInterface $entity,
			$options = array()) {
		
		// UNTIL I UNDERSTAND RULES, THE EXISTS-IN ARE KILLING PIECE CREATION
		if ($entity->isNew()) {
			$result = parent::save($entity, ['checkRules' => false]);
		} else {
			$result = parent::save($entity, $options);
		}
		return $result;
	}
}

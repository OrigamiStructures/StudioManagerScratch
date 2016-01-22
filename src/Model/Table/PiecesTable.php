<?php
namespace App\Model\Table;

use App\Model\Entity\Piece;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Collection\Collection;

define('NUMBERED_PIECES', 1);
define('OPEN_PIECES', 0);

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
				'assigned_piece_count'=> [$this, 'assignedPieces'],
				'fluid_piece_count'  => [$this, 'fluidPieces'],
				'collected_piece_count' => ['conditions' => ['collected' => 1]],
			],
            'Editions' => [
				'assigned_piece_count' => [$this, 'assignedPieces'],
				'fluid_piece_count'  => [$this, 'fluidPieces'],
			]
        ]);
//		$this->addBehavior('ArtworkStack');

//		if (!isset($this->SystemState) || $this->SystemState->is(ARTWORK_SAVE)) {
//		if ($this->SystemState->is(ARTWORK_SAVE)) {
			$this->belongsTo('Users', [
				'foreignKey' => 'user_id'
			]);
			$this->belongsTo('Editions', [
				'foreignKey' => 'edition_id'
			]);
			$this->belongsTo('Formats', [
				'foreignKey' => 'format_id'
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
	
	/**
	 * Callable that calcs CounterCache Piece values for Formats and Editions
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
	public function assignedPieces($event, $entity, $table) {
		if (is_null($entity->format_id)) {
			return 0;
		} else {
			$pieces = $table->find('all')->where([
				'edition_id' => $entity->edition_id,
				'format_id' => $entity->format_id,
				])->select(['id', 'format_id', 'edition_id', 'quantity']);
			$sum = (new Collection($pieces->toArray()))->reduce(
					function($accumulate, $value) {
						return $accumulate + $value->quantity;
					}, 0
				);
			return $sum;//die;
		}
	}

	public function fluidPieces($event, $entity, $table) {
		if (is_null($entity->format_id)) {
			return 0;
		} else {
			$pieces = $table->find('all')->where([
				'edition_id' => $entity->edition_id,
				'format_id' => $entity->format_id,
				'disposition_count' => 0,
				])->select(['id', 'format_id', 'edition_id', 'quantity']);
			$sum = (new Collection($pieces->toArray()))->reduce(
					function($accumulate, $value) {
						return $accumulate + $value->quantity;
					}, 0
				);
			return $sum;//die;
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
        $rules->add($rules->existsIn(['edition_id'], 'Editions'));
        $rules->add($rules->existsIn(['format_id'], 'Formats'));
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
			throw new \BadMethodCallException("You must pass \$option['format_id']");
		}
		$format_id = $options['format_id'];
		$edition_id = $this->Formats->find('parentEdition', $options)
				->select(['Editions.id'])
				->toArray()[0]['Editions']->id;
					
		if (isset($options['edition_id'])) {
			$conditions['edition_id'] = $options['edition_id'];
		}
		
//		find piece (format_id && format.disposed && piece.free) or (edition.fluid)
		$query->where(['Pieces.format_id' => $format_id, 'Pieces.disposition_count >' => 0] /*disposable*/)
			->orWhere(['Peices.edition_id' => $edition_id, 'Pieces.disposition_count' => 0]);
		osd($query);die;
				// ADD CONDITION TO DISCOVER PIECES THAT CAN STILL BE DISPOSED
		$conditions;
		// ===========================================================
		
		return $query->where($conditions);
	}
}

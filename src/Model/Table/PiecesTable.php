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
//		$this->addBehavior('ArtworkStack');

//        $this->belongsTo('Users', [
//            'foreignKey' => 'user_id'
//        ]);
//        $this->belongsTo('Editions', [
//            'foreignKey' => 'edition_id'
//        ]);
//        $this->belongsTo('Formats', [
//            'foreignKey' => 'format_id'
//        ]);
//        $this->hasMany('Dispositions', [
//            'foreignKey' => 'piece_id'
//        ]);
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
		$pieces = array_fill($start, $count, $columns);
		if ($numbered) {
			$number_edition = (new Collection($pieces))->map(function($piece, $index){
				$piece['number'] = $index;
				return $piece;
			});
			$pieces = $number_edition->toArray();
		}
		return $pieces;
	}
}

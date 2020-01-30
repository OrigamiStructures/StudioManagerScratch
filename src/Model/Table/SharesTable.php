<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Model\Table\AppTable;

/**
 * Shares Model
 *
 * @property \App\Model\Table\SupervisorsTable&\Cake\ORM\Association\BelongsTo $Supervisors
 * @property \App\Model\Table\ManagersTable&\Cake\ORM\Association\BelongsTo $Managers
 * @property \App\Model\Table\CategoriesTable&\Cake\ORM\Association\BelongsTo $Categories
 *
 * @method \App\Model\Entity\Share get($primaryKey, $options = [])
 * @method \App\Model\Entity\Share newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Share[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Share|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Share saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Share patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Share[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Share findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SharesTable extends AppTable
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

        $this->setTable('shares');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ShrSup', [
            'foreignKey' => 'supervisor_id',
            'className' => 'Members',
            'bindingKey' => 'id',
        ]);
        $this->belongsTo('ShrMngr', [
            'foreignKey' => 'manager_id',
            'className' => 'Members',
            'bindingKey' => 'id',
        ]);
        $this->belongsTo('ShrCat', [
            'foreignKey' => 'category_id',
            'className' => 'Members',
            'bindingKey' => 'id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

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
        return $rules;
    }

    /**
     * Add names to manifests so that the layer can be more useful in the stacks
     *
     * @param $query Query
     * @return $array The manifests ready for storage as a layer
     */
    public function configureLinkLayer($query) {
        $query = $query->contain(['ShrSup', 'ShrMngr', 'ShrCat']);

        $foundSet = collection($query->toArray());
        $shares = $foundSet->map(function ($share, $index) {
            $share->names = [
                $share->shr_sup->id => $share->shr_sup->name(),
                $share->shr_mngr->id => $share->shr_mngr->name(),
                $share->shr_cat->id => $share->shr_cat->name()
            ];
            unset($share->shr_sup, $share->shr_mngr, $share->shr_cat);
            return $share;
        });

        return $shares->toArray();
    }

}

<?php
namespace App\Model\Table;

use App\Constants\MemCon;
use App\Model\Entity\Member;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use Cake\Event\Event;
use ArrayObject;
use App\Model\Behavior\IntegerQueryBehavior;
use App\Model\Behavior\StringQueryBehavior;

/**
 * Members Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Images
 * @property \Cake\ORM\Association\HasMany $Dispositions
 * @property \Cake\ORM\Association\HasMany $Locations
 * @property \Cake\ORM\Association\BelongsToMany $Groups
 */
class MembersTable extends AppTable
{
    private $_person_containment = ['Addresses', 'Contacts', 'Groups' => ['GroupIdentities']];

    private $_complete_containment = ['Addresses', 'Contacts', 'Groups' => ['GroupIdentities'], 'ProxyGroups' => ['Members']];

// <editor-fold defaultstate="collapsed" desc="Core">

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)     {
        parent::initialize($config);
        $this->_intializeProperties();
        $this->_initializeBehaviors();
        $this->_initializeAssociations();
    }


// <editor-fold defaultstate="collapsed" desc="Initialization details">


    protected function _intializeProperties() {
        $this->setTable('members');
        $this->setPrimaryKey('id');
    }

    protected function _initializeBehaviors() {
        $this->addBehavior('IntegerQuery');
        $this->addBehavior('Timestamp');
    }

    protected function _initializeAssociations() {

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Images', [
            'foreignKey' => 'image_id'
        ]);
        $this->hasMany('Dispositions', [
            'foreignKey' => 'member_id'
        ]);
        $this->hasMany('Addresses', [
            'foreignKey' => 'member_id',
            'dependent' => TRUE
        ]);
        $this->hasMany('Contacts', [
            'foreignKey' => 'member_id',
            'dependent' => TRUE
        ]);
        $this->hasOne('Users', [
            'foreignKey' => 'member_id'
        ]);
        $this->hasMany('ArtistManifests', [
            'className' => 'Manifests',
            'foreignKey' => 'member_id',
            'dependent' => TRUE,
        ]);
        $this->hasMany('ManagerManifests', [
            'className' => 'Manifests',
            'foreignKey' => 'manager_member',
            'dependent' => TRUE,
        ]);
        $this->hasMany('SupervisorManifests', [
            'className' => 'Manifests',
            'foreignKey' => 'supervisor_member',
            'dependent' => TRUE,
        ]);
        $this->belongsToMany('Memberships',
			['joinTable' => 'groups_members',
            'foreignKey' => 'member_id',
            'targetForeignKey' => 'group_id',
        ]);
        $this->hasMany('SharedCategory',
            [
                'className' => 'Shares',
                'foreignKey' => 'supervisor_id',
                'bindingKey' => 'id',
                'propertyName' => 'shared_categories',
                'dependent' => true
            ]);
        $this->hasMany('PermittedCategory',
            [
                'className' => 'Shares',
                'foreignKey' => 'manager_id',
                'bindingKey' => 'id',
                'propertyName' => 'permitted_categories',
                'dependent' => true
            ]);
        $this->hasMany('Shares',
            [
                'className' => 'Shares',
                'foreignKey' => 'category_id',
                'bindingKey' => 'id',
                'propertyName' => 'shares',
                'dependent' => true
            ]);
    }

// </editor-fold>

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)     {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', 'create')
            /* @todo allow foreign chars and such. but no code */
            ->regex('first_name',
                '/[a-zA-Z0-9 -]*/',
                'Only alphanumeric characters, spaces, and hypens are allowed')
            ->regex('last_name',
                '/[a-zA-Z0-9 -]*/',
                'Only alphanumeric characters, spaces, and hypens are allowed')
            ->add('member_type', 'validType', [
                'rule' => 'isValidType',
                'message' => __('You need to provide a valid member type'),
                'provider' => 'table'
            ])
        ;
        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)     {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['image_id'], 'Images'));
        $rules->addDelete([$this, 'deleteRule']);
        //unique for a single data owner
        $rules->add([$this, 'uniqueMemberName'], 'UniqueCategory', [
            'errorField' => 'last_name',
            'message' => 'The name must be unique.'
        ]);
        return $rules;
    }

    public function isValidType($value, array $context)
    {
        return in_array($value, MemCon::TYPES, true);
    }

    /**
     * Rule to ensure unique names for the data owner
     *
     * dups are allowed in the table, but all members for an owner must be unique
     *
     * @todo do we ever want to ensure uniqueness among owned AND shared members?
     *
     * @param $entity
     * @param $table
     * @return bool
     */
    public function uniqueMemberName($entity, $options)
    {
        $user_id = $entity->user_id;
        $members = $options['repository']->find('all')
            ->where(['user_id' => $entity->user_id]);
        if ($members->count() > 0) {
            $memberCollection = collection($members->toArray());
            $result = $memberCollection->reduce(function ($bool, $member) use ($entity) {
                return $bool
                    && $member->first_name != $entity->first_name
                    &&  $member->first_name != $entity->first_name;
            }, true);
        } else {
            $result = true;
        }
        if (!$result) {
            $result = $entity->name() . ' already exists. Name must be unique.';
        }
        return $result;
    }


    public function deleteRule($entity, $options) {
        return $entity->user_id === $this->contextUser()->artistId();
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Custom Finders (legacy methods)">

    /**
     * Custom finder for the member review action
     *
     * Returns either a list of members or a specific member based upon the existance
     * of the member query argument
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findMemberReview(Query $query, array $options) {
        $query = $this->findMemberList($query, $options);
        $query = $this->findContainment($query, $options);
        $query->orderAsc('last_name');
        return $query;
    }


    /**
     * Custom finder for the memberList
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findMemberList(Query $query, array $options) {
        $query->where([
            'Members.active' => 1,
            'Members.user_id' => $this->contextUser()->artistId()
        ]);
        return $query;
    }


    /**
     * Custom finder to setup containment based upon member type
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findContainment(Query $query, array $options) {
        if (isset($options['member'])) {
            $member_id = $options['member'];
            $query->where([
                'Members.id' => $member_id
            ]);
            $query->contain($this->_complete_containment);
        } else {
            $query->contain($this->_complete_containment);
        }
        return $query;
    }

    public function findSearch(Query $query, $options) {
            $query->where(['first_name LIKE' => "%{$options[0]}%"])
                      ->orWhere(['last_name LIKE' => "%{$options[0]}%"]);
            return $query->toArray();
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Custom Finders">

    /**
     * Find members by id
     *
     * @param Query $query
     * @param array $options Pass args on $options['values'] = [ ]
     * @return Query
     */
    public function findMembers(Query $query, $options) {
        return $this->integer($query, 'id', $options['values']);
    }

    /**
     * Find images by id
     *
     * @param Query $query
     * @param array $options Pass args on $options['values'] = [ ]
     * @return Query
     */
    public function findhasImages(Query $query, $options) {
        return $this->integer($query, 'image_id', $options['values']);
    }

    /**
     * Find collector by quantity collected
     *
     * Members counts how many pieces a member has collected.
     *
     * @param Query $query
     * @param array $options Pass args on $options['values'] = [ ]
     * @return Query
     */
    public function findCollectors(Query $query, $options) {
        return $this->integer($query, 'collector', $options['values']);
    }

    /**
     * Find disposition count
     *
     * @param Query $query
     * @param array $options Pass args on $options['values'] = [ ]
     * @return Query
     */
    public function findDispositionCounts(Query $query, $options) {
        return $this->integer($query, 'disposition_count', $options['values']);
    }

    /**
     * Find member types
     *
     * @param Query $query
     * @param array $options Pass args on $options['value'] = 'string-arg'
     * @return Query
     */
    public function findType(Query $query, $options) {
        return $this->string($query, 'member_type', $options['value']);
    }

    /**
     * Find active group records
     *
     * @param Query $query
     * @param array $options None required (or used)
     * @return Query
     */
    public function findActiveGroups(Query $query, $options) {
        return $this->string->findType($query, 'member_type NOT', 'Person')
            ->findByActive(1);
    }

    /**
     * Find inactive group records
     *
     * @param Query $query
     * @param array $options None required (or used)
     * @return Query
     */
    public function findInactiveGroups(Query $query, $options) {
        return $this->string->findType($query, 'member_type NOT', 'Person')
            ->findByActive(0);
    }

    /**
     * Find active people records
     *
     * @param Query $query
     * @param array $options None required (or used)
     * @return Query
     */
    public function findActivePeople(Query $query, $options) {
        return $this->string->findType($query, 'member_type', 'Person')
            ->findByActive(1);
    }

    /**
     * Find inactive people records
     *
     * @param Query $query
     * @param array $options None required (or used)
     * @return Query
     */
    public function findInactivePeople(Query $query, $options) {
        return $this->string->findType($query, 'member_type', 'Person')
            ->findByActive(0);
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Dynamic finders">

    /**
     * I'm leaving first_name, last_name, and active
     * for dynamic finders to handle
     */

// </editor-fold>

    /**
     * Modify the provided string and return it as a properly sortable name
     *
     * for example, drop leading 'The ' bits.
     *
     * @param string $name
     * @return string
     */
    private function createSortName($name) {
        return $name;
    }

    /**
     * Complete the member entity for creation and editing
     *
     * When an entity is built for member creation, or an entity found for editing. Make sure
     * it has at least one address and two contact records for easy editing of the complete
     * package.
     *
     * @param Entity $entity
     * @param string $type the member type
     * @return Entity
     */
    public function defaultMemberEntity($entity, $type) {
        $dme = [
            'member_type' => $type,
            'first_name' => NULL,
            'contacts' => [
                [
                    'user_id' => $this->contextUser()->artistId(),
                    'label' => 'email',
                    'primary' => 1
                ],
                [
                    'user_id' => $this->contextUser()->artistId(),
                    'label' => 'phone'
                ]
            ],
            'addresses' => [
                [
                    'user_id' => $this->contextUser()->artistId(),
                    'label' => 'main',
                    'primary' => 1
                ]
            ]
        ];
        if(
            in_array($type, [MEMBER_TYPE_CATEGORY, MEMBER_TYPE_ORGANIZATION])
            && $this->ContextUser->actionIs('create')//this is a bullshit call and
                        //this request processing should never happen in the Model
        ){
            $proxy_group = [
                'user_id' => $this->contextUser()->artistId()
            ];
            $proxy_group_entity = new \Cake\ORM\Entity($proxy_group);
            $entity->set('proxy_group', $proxy_group_entity);
        }
        $entity = $this->patchEntity($entity, $dme);
        return $entity;
    }

	public function findHook(Query $query, array $options) {

		$result = $query
            ->where(['active' => 1]);
		return $result;
	}

}

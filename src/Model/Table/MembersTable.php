<?php
namespace App\Model\Table;

use App\Model\Entity\Member;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
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
 * @property \Cake\ORM\Association\HasMany $Users
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
        $this->setDisplayField('name');
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
//        $this->hasMany('Locations', [
//            'foreignKey' => 'member_id'
//        ]);
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
		$this->hasOne('Manifests', [
			'foreignKey' => 'member_id',
			'dependent' => TRUE,
		]);
        $this->belongsToMany('Memberships',
			['joinTable' => 'groups_members',
            'foreignKey' => 'member_id',
            'targetForeignKey' => 'group_id',]);
//        $this->belongsToMany('Groups', [
//            'className' => 'Groups',
//            'joinTable' => 'groups_members'
//        ]);
//        $this->hasOne('ProxyGroups', [
//            'className' => 'ProxyGroups',
//            'foreignKey' => 'member_id',
//            'propertyName' => 'proxy_group',
//            'dependent' => TRUE
//        ]);
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
            ->allowEmptyString('name');

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
        return $rules;
    }


    public function deleteRule($entity, $options) {
        return $entity->user_id === $this->contextUser()->artistId();
    }


    public function implementedEvents() {
        $events = [
            'Model.beforeMarshal' => 'beforeMarshal',
        ];
        return array_merge(parent::implementedEvents(), $events);
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Lifecycle">

    /**
     * Implemented beforeMarshal event
     *
     * @param \App\Model\Table\Event $event
     * @param \App\Model\Table\ArrayObject $data
     * @param \App\Model\Table\ArrayObject $options
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options) {
        $this->bmSetupGroup($data);
        $this->bmSetupSort($data);
        $data['user_id'] = $this->contextUser()->artistId();
    }


    /**
     * Setup the group element for User, Category and Institution
     *
     * @param ArrayObject $data
     */
    private function bmSetupGroup(ArrayObject $data) {
        switch ($data['member_type']) {
            case MEMBER_TYPE_USER:
            case MEMBER_TYPE_CATEGORY:
            case MEMBER_TYPE_INSTITUTION:
                $data['group'] = isset($data['group']) ? $data['group'] : ['id' => NULL];
                $data['group']['user_id'] = $this->contextUser()->artistId();
                break;
            case MEMBER_TYPE_PERSON:
                break;
        }
    }


    /**
     * Setup the last_name as a sorting name for Categories and Institutions
     *
     * @param ArrayObject $data
     */
    private function bmSetupSort(ArrayObject $data) {
        switch ($data['member_type']) {
            case MEMBER_TYPE_CATEGORY:
            case MEMBER_TYPE_INSTITUTION:
                $data['last_name'] = $this->createSortName($data['first_name']);
                break;
            case MEMBER_TYPE_USER:
            case MEMBER_TYPE_PERSON:
                break;
        }
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
        if ($this->SystemState->urlArgIsKnown('member')) {
            $member_id = $this->SystemState->queryArg('member');
            $query->where([
                'Members.id' => $member_id
            ]);


            /**
             * The type arg is never included so this always does 'group' containment
             * and Disposition containment doesn't work
             */
//            if($this->SystemState->queryArg('type') === MEMBER_TYPE_PERSON){
//                $query->contain($this->_person_containment);
//                $query->contain($this->_persons_disposition);
//            } else {
            $query->contain($this->_complete_containment);
//                $query->contain($this->_groups_disposition);
//            }
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
        if(in_array($type, [MEMBER_TYPE_CATEGORY, MEMBER_TYPE_INSTITUTION]) && $this->SystemState->is(MEMBER_CREATE)){
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

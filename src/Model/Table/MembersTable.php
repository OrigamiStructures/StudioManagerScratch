<?php
namespace App\Model\Table;

use App\Model\Entity\Member;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use ArrayObject;

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
    public function implementedEvents()
    {
		$events = [
            'Model.beforeMarshal' => 'beforeMarshal',
        ];
		return array_merge(parent::implementedEvents(), $events);
    }


    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('members');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Images', [
            'foreignKey' => 'image_id'
        ]);
        $this->hasMany('Dispositions', [
            'foreignKey' => 'member_id'
        ]);
        $this->hasMany('Locations', [
            'foreignKey' => 'member_id'
        ]);
        $this->hasMany('Addresses', [
            'foreignKey' => 'member_id'
        ]);
        $this->hasMany('Contacts', [
            'foreignKey' => 'member_id'
        ]);
        $this->hasOne('Users', [
            'foreignKey' => 'member_id'
        ]);
        $this->belongsToMany('Groups', [
            'foreignKey' => 'member_id',
            'targetForeignKey' => 'group_id',
            'joinTable' => 'groups_members'
        ]);
        $this->hasOne('Groups',[
            'className' => 'Groups',
            'foreignKey' => 'member_id'
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
            ->allowEmpty('name');

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
        $rules->add($rules->existsIn(['image_id'], 'Images'));
        return $rules;
    }
    
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
        if($this->SystemState->isKnown('member')){
            $query->where([
               'Members.id' => $this->SystemState->queryArg('member')
            ]);
        }
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
            'Members.user_id' => $this->SystemState->artistId()
        ]);
        $query->contain(['Addresses', 'Contacts', 'Groups']);
        return $query;
    }
    
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
        $data['user_id'] = $this->SystemState->artistId();
	}
    
    /**
     * Setup the group element for User, Category and Instituion
     * 
     * @param ArrayObject $data
     */
    private function bmSetupGroup(ArrayObject $data) {
        switch ($data['member_type']) {
            case MEMBER_TYPE_USER:
            case MEMBER_TYPE_CATEGORY:
            case MEMBER_TYPE_INSTITUTION:
                $data['group'] = isset($data['group']) ? $data['group'] : ['id' => NULL];
                $data['group']['user_id'] = $this->SystemState->artistId();
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
        $contacts = [
            [
                'user_id' => $this->SystemState->artistId(),
                'label' => 'email',
                'primary' => 1
            ],
            [
                'user_id' => $this->SystemState->artistId(),
                'label' => 'phone'
            ]
        ];
        $addresses = [
            [
                'user_id' => $this->SystemState->artistId(),
                'label' => 'main',
                'primary' => 1
            ]
        ];
        $entity->set('member_type', $type);
        $entity->set('contacts', $contacts);
        $entity->set('addresses', $addresses);
        return $entity;
    }
	
}

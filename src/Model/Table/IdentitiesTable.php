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
class IdentitiesTable extends MembersTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)     {
        parent::initialize($config);
		$this->setTable('members');
    }

	public function findIdentity(Query $query, $options) {
        return $this->findMembers($query, 'image_id', $options['values']);
    }

}

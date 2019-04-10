<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CategoryCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CategoryCardsTable Test Case
 */
class CategoryCardsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CategoryCardsTable
     */
    public $CategoryCardsTable;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.identities',
		'app.users',
		'app.groups_members',
        'app.data_owners',
        'app.members',
		'app.contacts',
		'app.addresses',
		'app.dispositions'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CategoryCards') ? [] : ['className' => CategoryCardsTable::class];
        $this->CategoryCardsTable = TableRegistry::getTableLocator()->get('CategoryCards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CategoryCardsTable);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
		$this->CategoryCardsTable->initialize([]);

		$this->assertTrue(
			is_a(
				$this->CategoryCardsTable->Members,
				'App\Model\Table\MembersTable'
			),
			'The MembersTable object did not get initialized properly'
		);
		
		$this->assertTrue(
			$this->CategoryCardsTable->getSchema()->hasColumn('members'),
			'The schema did not get a members column added'
		);
		
		$this->assertTrue(
			$this->CategoryCardsTable->getSchema()->getColumnType('members') 
				=== 'layer',
			'The schema column `members` is not a `layer` type'
		);
		
		$this->assertTrue($this->CategoryCardsTable->hasSeed('members'));
		$this->assertTrue($this->CategoryCardsTable->hasSeed('member'));
		
    }
	
	/**
	 * CategoryCards adds 1 layer and 1 seed
	 * 
	 * members
	 */
	public function testCartegoryCardsFromIdentites() {
		
		$groups = $this->CategoryCardsTable
				->find(
						'stackFrom', 
						['layer' => 'identities', 'ids' => [3, 7]]
				);
		
		$this->assertTrue('identity', $groups->member(3)->primary());
		$this->assertEquals(2, $groups->count(), 
				'The set doesn\'t contain 2 CategoryCards as expected');
		$this->assertEquals(2, count($groups->member(3)->members()), 
				'The aniticipated 2 member group was not included');
		$this->assertEquals(0, count($groups->member(7)->members()), 
				'The aniticipated 0 member group was not included');
	}
	
	public function testCartegoryCardsFromMembers() {
		
		$groups = $this->CategoryCardsTable
				->find(
						'stackFrom', 
						['layer' => 'members', 'ids' => [1]]
				);

		$this->assertEquals(1, $groups->count(), 
				'The set doesn\'t contain 1 CategoryCards as expected when '
				. 'building up from members');
		$this->assertEquals(2, count($groups->member(3)->members()), 
				'The aniticipated 2 member group was not included when '
				. 'building up from members');
	}
	
//	App\Model\Lib\StackSet Object
//(
//    [_data:protected] => Array
//        (
//            [3] => App\Model\Entity\CategoryCard Object
//                (
//                    [identity] => App\Model\Lib\Layer Object
//                        (
//                            [_layer:protected] => identity
//                            [_className:protected] => Identity
//                            [_data:protected] => Array
//                                (
//                                    [3] => App\Model\Entity\Identity Object
//                                        (
//                                            [id] => 3
//                                            [user_id] => f22f9b46-345f-4c6f-9637-060ceacb21b2
//                                            [image_id] => 
//                                            [first_name] => 
//                                            [last_name] => Drake Family
//                                            [member_type] => Group
//                                            [active] => 1
//                                            [disposition_count] => 
//                                            [collector] => 
//                                        )
//                                )
//
//                            [_entityProperties:protected] => Array
//                                (
//                                    [0] => id
//                                    [1] => created
//                                    [2] => modified
//                                    [3] => user_id
//                                    [4] => image_id
//                                    [5] => first_name
//                                    [6] => last_name
//                                    [7] => member_type
//                                    [8] => active
//                                    [9] => disposition_count
//                                    [10] => collector
//                                )
//
//                            [primary:protected] => 
//                            [_errors:protected] => Array
//                                (
//                                )
//
//                        )
//
//                    [data_owner] => App\Model\Lib\Layer Object
//                        (
//                            [_layer:protected] => dataowner
//                            [_className:protected] => DataOwner
//                            [_data:protected] => Array
//                                (
//                                    [f22f9b46-345f-4c6f-9637-060ceacb21b2] => App\Model\Entity\DataOwner Object
//                                        (
//                                            [id] => f22f9b46-345f-4c6f-9637-060ceacb21b2
//                                            [username] => drakefamily
//                                        )
//
//                                )
//
//                            [_entityProperties:protected] => Array
//                                (
//                                    [0] => id
//                                    [1] => username
//                                )
//
//                            [primary:protected] => 
//                            [_errors:protected] => Array
//                                (
//                                )
//
//                        )
//
//                    [memberships] => Array
//                        (
//                        )
//
//                    [members] => App\Model\Lib\Layer Object
//                        (
//                            [_layer:protected] => member
//                            [_className:protected] => Member
//                            [_data:protected] => Array
//                                (
//                                    [1] => App\Model\Entity\Member Object
//                                        (
//                                            [id] => 1
//                                            [user_id] => f22f9b46-345f-4c6f-9637-060ceacb21b2
//                                            [image_id] => 
//                                            [first_name] => Don
//                                            [last_name] => Drake
//                                            [member_type] => Person
//                                            [active] => 1
//                                            [disposition_count] => 12
//                                            [collector] => 1
//                                        )
//
//                                    [2] => App\Model\Entity\Member Object
//                                        (
//                                            [id] => 2
//                                            [user_id] => f22f9b46-345f-4c6f-9637-060ceacb21b2
//                                            [image_id] => 
//                                            [first_name] => Gail
//                                            [last_name] => Drake
//                                            [member_type] => Person
//                                            [active] => 1
//                                            [disposition_count] => 1
//                                            [collector] => 0
//                                        )
//                                )
//
//                            [_entityProperties:protected] => Array
//                                (
//                                    [0] => id
//                                    [1] => created
//                                    [2] => modified
//                                    [3] => user_id
//                                    [4] => image_id
//                                    [5] => first_name
//                                    [6] => last_name
//                                    [7] => member_type
//                                    [8] => active
//                                    [9] => disposition_count
//                                    [10] => collector
//                                )
//
//                            [primary:protected] => 
//                            [_errors:protected] => Array
//                                (
//                                )
//                        )
//                )
//
//            [7] => App\Model\Entity\CategoryCard Object
//                (
//                    [identity] => App\Model\Lib\Layer Object
//                        (
//                            [_layer:protected] => identity
//                            [_className:protected] => Identity
//                            [_data:protected] => Array
//                                (
//                                    [7] => App\Model\Entity\Identity Object
//                                        (
//                                            [id] => 7
//                                            [user_id] => f22f9b46-345f-4c6f-9637-060ceacb21b2
//                                            [image_id] => 
//                                            [first_name] => random text
//                                            [last_name] => Collectors
//                                            [member_type] => Group
//                                            [active] => 1
//                                            [disposition_count] => 0
//                                            [collector] => 0
//                                        )
//
//                                )
//
//                            [_entityProperties:protected] => Array
//                                (
//                                    [0] => id
//                                    [1] => created
//                                    [2] => modified
//                                    [3] => user_id
//                                    [4] => image_id
//                                    [5] => first_name
//                                    [6] => last_name
//                                    [7] => member_type
//                                    [8] => active
//                                    [9] => disposition_count
//                                    [10] => collector
//                                )
//
//                            [primary:protected] => 
//                            [_errors:protected] => Array
//                                (
//                                )
//
//                        )
//
//                    [data_owner] => App\Model\Lib\Layer Object
//                        (
//                            [_layer:protected] => dataowner
//                            [_className:protected] => DataOwner
//                            [_data:protected] => Array
//                                (
//                                    [f22f9b46-345f-4c6f-9637-060ceacb21b2] => App\Model\Entity\DataOwner Object
//                                        (
//                                            [id] => f22f9b46-345f-4c6f-9637-060ceacb21b2
//                                            [username] => drakefamily
//                                        )
//                                )
//
//                            [_entityProperties:protected] => Array
//                                (
//                                    [0] => id
//                                    [1] => username
//                                )
//
//                            [primary:protected] => 
//                            [_errors:protected] => Array
//                                (
//                                )
//
//                        )
//
//                    [memberships] => Array
//                        (
//                        )
//
//                    [members] => Array
//                        (
//                        )
//                )
//        )
//
//    [_stackName:protected] => identity
//    [primary:protected] => 
//)
}

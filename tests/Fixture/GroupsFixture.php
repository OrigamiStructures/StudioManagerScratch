<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * GroupsFixture
 *
 */
class GroupsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'user_id' => ['type' => 'uuid', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'member_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'active' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_swedish_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 3,
                'active' => true
            ],
            [
                'id' => 2,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 4,
                'active' => true
            ],
            [
                'id' => 3,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 5,
                'active' => true
            ],
            [
                'id' => 4,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 6,
                'active' => true
            ],
            [
                'id' => 5,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 7,
                'active' => true
            ],
        ];
        parent::init();
    }
}

<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MembersFixture
 *
 */
class MembersFixture extends TestFixture
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
        'image_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'first_name' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'last_name' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'member_type' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'active' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null],
        'disposition_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'collector' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'is_artist' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'supervisor_id' => ['type' => 'uuid', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'manager_id' => ['type' => 'uuid', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
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
                'created' => '2016-03-08 01:20:42',
                'modified' => '2016-03-08 01:20:42',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => 9,
                'first_name' => 'Don',
                'last_name' => 'Drake',
                'member_type' => 'Person',
                'active' => true,
                'disposition_count' => 12,
                'collector' => 1,
                'is_artist' => true,
                'supervisor_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'manager_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2'
            ],
            [
                'id' => 2,
                'created' => '2016-03-08 01:20:58',
                'modified' => '2016-03-08 01:20:58',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => null,
                'first_name' => 'Gail',
                'last_name' => 'Drake',
                'member_type' => 'Person',
                'active' => true,
                'disposition_count' => 1,
                'collector' => 0,
                'is_artist' => true,
                'supervisor_id' => null,
                'manager_id' => null
            ],
            [
                'id' => 3,
                'created' => '2016-03-08 01:21:08',
                'modified' => '2016-03-09 18:23:37',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => null,
                'first_name' => '',
                'last_name' => 'Drake Family',
                'member_type' => 'Category',
                'active' => true,
                'disposition_count' => null,
                'collector' => null,
                'is_artist' => null,
                'supervisor_id' => null,
                'manager_id' => null
            ],
            [
                'id' => 4,
                'created' => '2016-03-08 01:21:19',
                'modified' => '2016-03-08 01:21:19',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => null,
                'first_name' => 'Wonderland Group',
                'last_name' => 'Wonderland Group',
                'member_type' => 'Institution',
                'active' => true,
                'disposition_count' => 0,
                'collector' => 0,
                'is_artist' => null,
                'supervisor_id' => null,
                'manager_id' => null
            ],
            [
                'id' => 5,
                'created' => '2016-03-08 01:21:33',
                'modified' => '2016-03-08 01:21:33',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => null,
                'first_name' => 'Alice Goask',
                'last_name' => 'Alice Goask',
                'member_type' => 'Institution',
                'active' => true,
                'disposition_count' => 0,
                'collector' => 0,
                'is_artist' => null,
                'supervisor_id' => null,
                'manager_id' => null
            ],
            [
                'id' => 6,
                'created' => '2016-03-09 18:14:26',
                'modified' => '2016-03-09 18:14:26',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => null,
                'first_name' => 'random text',
                'last_name' => 'SFMOMA',
                'member_type' => 'Institution',
                'active' => true,
                'disposition_count' => null,
                'collector' => null,
                'is_artist' => null,
                'supervisor_id' => null,
                'manager_id' => null
            ],
            [
                'id' => 7,
                'created' => '2016-03-09 18:15:08',
                'modified' => '2016-03-09 18:18:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => null,
                'first_name' => 'random text',
                'last_name' => 'Collectors',
                'member_type' => 'Category',
                'active' => true,
                'disposition_count' => 0,
                'collector' => 0,
                'is_artist' => null,
                'supervisor_id' => null,
                'manager_id' => null
            ],
            [
                'id' => 8,
                'created' => '2016-03-10 18:04:14',
                'modified' => '2016-03-10 18:04:14',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => null,
                'first_name' => 'Kate',
                'last_name' => 'Jordahl',
                'member_type' => 'Person',
                'active' => true,
                'disposition_count' => 4,
                'collector' => 1,
                'is_artist' => null,
                'supervisor_id' => null,
                'manager_id' => null
            ],
            [
                'id' => 9,
                'created' => '2016-03-21 18:14:52',
                'modified' => '2016-08-22 19:23:46',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => null,
                'first_name' => 'Rae',
                'last_name' => 'Trujillo',
                'member_type' => 'Person',
                'active' => false,
                'disposition_count' => 2,
                'collector' => 2,
                'is_artist' => null,
                'supervisor_id' => null,
                'manager_id' => null
            ],
            [
                'id' => 75,
                'created' => '2016-03-21 18:14:52',
                'modified' => '2016-08-22 19:23:46',
                'user_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'image_id' => null,
                'first_name' => 'Leonardo',
                'last_name' => 'DiVinci',
                'member_type' => 'Person',
                'active' => false,
                'disposition_count' => 0,
                'collector' => 0,
                'is_artist' => true,
                'supervisor_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'manager_id' => '708cfc57-1162-4c5b-9092-42c25da131a9'

            ],
        ];
        parent::init();
    }
}

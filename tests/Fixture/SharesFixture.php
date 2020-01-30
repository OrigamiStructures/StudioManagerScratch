<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SharesFixture
 */
class SharesFixture extends TestFixture
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
        'user_id' => ['type' => 'uuid', 'length' => null, 'null' => true, 'default' => '', 'comment' => '', 'precision' => null],
        'supervisor_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'member_id for the supervisor identity', 'precision' => null, 'autoIncrement' => null],
        'manager_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'member_id for the manager identity', 'precision' => null, 'autoIncrement' => null],
        'category_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'member_id for the shared category', 'precision' => null, 'autoIncrement' => null],
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
                'id' => 9,
                'created' => '2020-01-29 22:37:20',
                'modified' => '2020-01-29 22:37:20',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'supervisor_id' => 1,
                'manager_id' => 75,
                'category_id' => 105
            ],
            [
                'id' => 10,
                'created' => '2020-01-29 22:37:42',
                'modified' => '2020-01-29 22:37:42',
                'user_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'supervisor_id' => 75,
                'manager_id' => 1,
                'category_id' => 106
            ],
            [
                'id' => 11,
                'created' => '2020-01-30 05:06:27',
                'modified' => '2020-01-30 05:06:27',
                'user_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'supervisor_id' => 75,
                'manager_id' => 1,
                'category_id' => 107
            ],
            [
                'id' => 12,
                'created' => '2020-01-30 05:06:27',
                'modified' => '2020-01-30 05:06:27',
                'user_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'supervisor_id' => 75,
                'manager_id' => 9,
                'category_id' => 107
            ],
        ];
        parent::init();
    }
}

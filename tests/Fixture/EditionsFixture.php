<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EditionsFixture
 *
 */
class EditionsFixture extends TestFixture
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
        'title' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'type' => ['type' => 'string', 'length' => 127, 'null' => true, 'default' => 'Unique', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'quantity' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'artwork_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'series_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'assigned_piece_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'format_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'fluid_piece_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => 'pieces with no dispositions can easily move to other formats', 'precision' => null, 'autoIncrement' => null],
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
                'id' => 5,
                'created' => '2016-03-08 02:16:39',
                'modified' => '2016-03-15 03:40:37',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'type' => 'Unique',
                'quantity' => 1,
                'artwork_id' => 4,
                'series_id' => null,
                'assigned_piece_count' => 1,
                'format_count' => 1,
                'fluid_piece_count' => 1
            ],
            [
                'id' => 6,
                'created' => '2016-03-08 02:17:15',
                'modified' => '2016-08-19 20:56:53',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '2016 Mini Book',
                'type' => 'Limited Edition',
                'quantity' => 15,
                'artwork_id' => 5,
                'series_id' => null,
                'assigned_piece_count' => 15,
                'format_count' => 1,
                'fluid_piece_count' => 0
            ],
            [
                'id' => 7,
                'created' => '2016-03-08 02:18:03',
                'modified' => '2016-03-08 02:18:03',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'PhotoCentral Fundraiser',
                'type' => 'Limited Edition',
                'quantity' => 10,
                'artwork_id' => 6,
                'series_id' => null,
                'assigned_piece_count' => 10,
                'format_count' => 1,
                'fluid_piece_count' => 10
            ],
            [
                'id' => 8,
                'created' => '2016-03-08 02:18:19',
                'modified' => '2016-03-15 03:40:37',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'PopOpen',
                'type' => 'Open Edition',
                'quantity' => 150,
                'artwork_id' => 4,
                'series_id' => null,
                'assigned_piece_count' => 150,
                'format_count' => 1,
                'fluid_piece_count' => 148
            ],
            [
                'id' => 10,
                'created' => '2016-03-09 21:24:23',
                'modified' => '2016-10-23 03:33:51',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Hardcover',
                'type' => 'Limited Edition',
                'quantity' => 100,
                'artwork_id' => 7,
                'series_id' => null,
                'assigned_piece_count' => 100,
                'format_count' => 1,
                'fluid_piece_count' => 68
            ],
            [
                'id' => 11,
                'created' => '2016-03-09 22:27:42',
                'modified' => '2016-03-10 18:59:12',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Hardcover',
                'type' => 'Limited Edition',
                'quantity' => 100,
                'artwork_id' => 8,
                'series_id' => null,
                'assigned_piece_count' => 100,
                'format_count' => 1,
                'fluid_piece_count' => 62
            ],
            [
                'id' => 12,
                'created' => '2016-03-10 19:00:23',
                'modified' => '2016-10-05 20:15:41',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'type' => 'Limited Edition',
                'quantity' => 100,
                'artwork_id' => 9,
                'series_id' => null,
                'assigned_piece_count' => 100,
                'format_count' => 1,
                'fluid_piece_count' => 73
            ],
            [
                'id' => 13,
                'created' => '2016-03-10 19:01:40',
                'modified' => '2016-10-05 20:05:17',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Hard Bound',
                'type' => 'Limited Edition',
                'quantity' => 100,
                'artwork_id' => 10,
                'series_id' => null,
                'assigned_piece_count' => 100,
                'format_count' => 1,
                'fluid_piece_count' => 74
            ],
            [
                'id' => 14,
                'created' => '2016-03-10 19:03:10',
                'modified' => '2016-10-05 20:04:38',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Paper Bound',
                'type' => 'Open Edition',
                'quantity' => 300,
                'artwork_id' => 10,
                'series_id' => null,
                'assigned_piece_count' => 300,
                'format_count' => 1,
                'fluid_piece_count' => 219
            ],
            [
                'id' => 15,
                'created' => '2016-03-10 19:06:11',
                'modified' => '2016-10-05 20:17:16',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Paper Bound',
                'type' => 'Open Edition',
                'quantity' => 300,
                'artwork_id' => 9,
                'series_id' => null,
                'assigned_piece_count' => 300,
                'format_count' => 1,
                'fluid_piece_count' => 257
            ],
            [
                'id' => 16,
                'created' => '2016-03-10 19:06:49',
                'modified' => '2016-10-05 19:54:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Paper Bound',
                'type' => 'Open Edition',
                'quantity' => 300,
                'artwork_id' => 8,
                'series_id' => null,
                'assigned_piece_count' => 300,
                'format_count' => 1,
                'fluid_piece_count' => 161
            ],
            [
                'id' => 17,
                'created' => '2016-03-10 19:07:50',
                'modified' => '2016-10-23 03:26:52',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Paper Bound',
                'type' => 'Open Edition',
                'quantity' => 300,
                'artwork_id' => 7,
                'series_id' => null,
                'assigned_piece_count' => 300,
                'format_count' => 1,
                'fluid_piece_count' => 224
            ],
            [
                'id' => 18,
                'created' => '2016-06-02 15:19:21',
                'modified' => '2016-06-02 15:19:21',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'type' => 'Limited Edition',
                'quantity' => 50,
                'artwork_id' => 11,
                'series_id' => null,
                'assigned_piece_count' => 50,
                'format_count' => 1,
                'fluid_piece_count' => 49
            ],
            [
                'id' => 20,
                'created' => '2016-08-19 20:59:30',
                'modified' => '2016-08-19 20:59:30',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Artist\'s Proof',
                'type' => 'Unique',
                'quantity' => 1,
                'artwork_id' => 5,
                'series_id' => null,
                'assigned_piece_count' => 1,
                'format_count' => 1,
                'fluid_piece_count' => 1
            ],
            [
                'id' => 21,
                'created' => '2016-08-28 01:02:44',
                'modified' => '2016-09-22 15:34:10',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'type' => 'Limited Edition',
                'quantity' => 15,
                'artwork_id' => 13,
                'series_id' => null,
                'assigned_piece_count' => 15,
                'format_count' => 1,
                'fluid_piece_count' => 7
            ],
            [
                'id' => 22,
                'created' => '2016-09-29 03:55:03',
                'modified' => '2016-09-29 03:55:03',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Open Fine Leather',
                'type' => 'Limited Edition',
                'quantity' => 3,
                'artwork_id' => 14,
                'series_id' => null,
                'assigned_piece_count' => 3,
                'format_count' => 1,
                'fluid_piece_count' => 1
            ],
            [
                'id' => 23,
                'created' => '2016-10-05 16:26:31',
                'modified' => '2016-10-05 16:26:31',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Hardbound',
                'type' => 'Limited Edition',
                'quantity' => 100,
                'artwork_id' => 15,
                'series_id' => null,
                'assigned_piece_count' => 100,
                'format_count' => 1,
                'fluid_piece_count' => 100
            ],
            [
                'id' => 24,
                'created' => '2016-10-05 16:29:50',
                'modified' => '2016-10-05 16:29:50',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Hardbound',
                'type' => 'Limited Edition',
                'quantity' => 100,
                'artwork_id' => 16,
                'series_id' => null,
                'assigned_piece_count' => 100,
                'format_count' => 1,
                'fluid_piece_count' => 71
            ],
            [
                'id' => 25,
                'created' => '2016-10-05 16:38:50',
                'modified' => '2016-10-05 16:38:50',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'type' => 'Limited Edition',
                'quantity' => 100,
                'artwork_id' => 17,
                'series_id' => null,
                'assigned_piece_count' => 100,
                'format_count' => 1,
                'fluid_piece_count' => 70
            ],
            [
                'id' => 26,
                'created' => '2016-10-05 19:31:01',
                'modified' => '2016-10-05 20:19:08',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'type' => 'Open Edition',
                'quantity' => 300,
                'artwork_id' => 16,
                'series_id' => null,
                'assigned_piece_count' => 300,
                'format_count' => 1,
                'fluid_piece_count' => 242
            ],
            [
                'id' => 27,
                'created' => '2016-10-05 19:37:53',
                'modified' => '2016-10-05 19:37:53',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'type' => 'Open Edition',
                'quantity' => 300,
                'artwork_id' => 17,
                'series_id' => null,
                'assigned_piece_count' => 300,
                'format_count' => 1,
                'fluid_piece_count' => 239
            ],
            [
                'id' => 28,
                'created' => '2016-10-05 19:49:43',
                'modified' => '2016-10-23 03:26:52',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Unbound',
                'type' => 'Open Edition',
                'quantity' => 400,
                'artwork_id' => 7,
                'series_id' => null,
                'assigned_piece_count' => 400,
                'format_count' => 1,
                'fluid_piece_count' => 129
            ],
            [
                'id' => 29,
                'created' => '2016-10-05 19:55:28',
                'modified' => '2016-10-05 19:55:28',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Unbound',
                'type' => 'Open Edition',
                'quantity' => 400,
                'artwork_id' => 8,
                'series_id' => null,
                'assigned_piece_count' => 400,
                'format_count' => 1,
                'fluid_piece_count' => 375
            ],
            [
                'id' => 30,
                'created' => '2016-10-05 19:56:58',
                'modified' => '2016-10-05 20:04:38',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Unbound',
                'type' => 'Open Edition',
                'quantity' => 400,
                'artwork_id' => 10,
                'series_id' => null,
                'assigned_piece_count' => 400,
                'format_count' => 1,
                'fluid_piece_count' => 364
            ],
            [
                'id' => 31,
                'created' => '2016-10-05 20:16:55',
                'modified' => '2016-10-05 20:16:55',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Unbound',
                'type' => 'Open Edition',
                'quantity' => 400,
                'artwork_id' => 9,
                'series_id' => null,
                'assigned_piece_count' => 400,
                'format_count' => 1,
                'fluid_piece_count' => 375
            ],
            [
                'id' => 32,
                'created' => '2016-10-05 20:19:44',
                'modified' => '2016-10-05 20:19:44',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Unbound',
                'type' => 'Open Edition',
                'quantity' => 400,
                'artwork_id' => 16,
                'series_id' => null,
                'assigned_piece_count' => 400,
                'format_count' => 1,
                'fluid_piece_count' => 375
            ],
            [
                'id' => 33,
                'created' => '2016-10-05 20:20:56',
                'modified' => '2016-10-05 20:20:56',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Unbound',
                'type' => 'Open Edition',
                'quantity' => 400,
                'artwork_id' => 17,
                'series_id' => null,
                'assigned_piece_count' => 400,
                'format_count' => 1,
                'fluid_piece_count' => 375
            ],
            [
                'id' => 34,
                'created' => '2016-10-06 14:43:31',
                'modified' => '2016-10-06 14:43:31',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'type' => 'Limited Edition',
                'quantity' => 50,
                'artwork_id' => 18,
                'series_id' => null,
                'assigned_piece_count' => 50,
                'format_count' => 1,
                'fluid_piece_count' => 50
            ],
            [
                'id' => 35,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-05-14 04:59:12',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Test Limited',
                'type' => 'Limited Edition',
                'quantity' => 50,
                'artwork_id' => 19,
                'series_id' => null,
                'assigned_piece_count' => 10,
                'format_count' => 2,
                'fluid_piece_count' => 9
            ],
            [
                'id' => 36,
                'created' => '2018-04-30 15:46:06',
                'modified' => '2018-04-30 15:46:06',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Deluxe',
                'type' => 'Limited Edition',
                'quantity' => 3,
                'artwork_id' => 19,
                'series_id' => null,
                'assigned_piece_count' => 3,
                'format_count' => 1,
                'fluid_piece_count' => 3
            ],
        ];
        parent::init();
    }
}

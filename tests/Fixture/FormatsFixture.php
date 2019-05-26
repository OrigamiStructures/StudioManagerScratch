<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FormatsFixture
 *
 */
class FormatsFixture extends TestFixture
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
        'description' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null],
        'range_flag' => ['type' => 'tinyinteger', 'length' => 4, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'boolean to indicate the use of range values', 'precision' => null],
        'range_start' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'range_end' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'image_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'edition_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'subscription_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'assigned_piece_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'fluid_piece_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => 'pieces with no dispositions can easily move to other formats', 'precision' => null, 'autoIncrement' => null],
        'collected_piece_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
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
                'created' => '2016-03-08 02:16:39',
                'modified' => '2016-03-08 02:16:39',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => null,
                'description' => 'Watercolor 6 x 15"',
                'range_flag' => 1,
                'range_start' => null,
                'range_end' => null,
                'image_id' => null,
                'edition_id' => 5,
                'subscription_id' => null,
                'assigned_piece_count' => 1,
                'fluid_piece_count' => 1,
                'collected_piece_count' => 0
            ],
            [
                'id' => 2,
                'created' => '2016-03-08 02:18:19',
                'modified' => '2016-03-08 02:18:19',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Card Bound',
                'description' => 'Digital output with cloth-covered card stock covers',
                'range_flag' => 2,
                'range_start' => null,
                'range_end' => null,
                'image_id' => 8,
                'edition_id' => 8,
                'subscription_id' => null,
                'assigned_piece_count' => 150,
                'fluid_piece_count' => 149,
                'collected_piece_count' => 1
            ],
            [
                'id' => 3,
                'created' => '2016-03-08 02:17:15',
                'modified' => '2016-08-19 20:56:53',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Mini Box',
                'description' => 'Paper covered container with 4 trays. Trays display mounted and lacquered digital content on the front and QR codes which link web addresses on the reverse',
                'range_flag' => 3,
                'range_start' => null,
                'range_end' => null,
                'image_id' => null,
                'edition_id' => 6,
                'subscription_id' => null,
                'assigned_piece_count' => 15,
                'fluid_piece_count' => 0,
                'collected_piece_count' => 6
            ],
            [
                'id' => 4,
                'created' => '2016-08-19 20:59:30',
                'modified' => '2016-08-19 20:59:30',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'description' => 'Prototype made while developing edition details.
Paper covered container with 4 trays. Trays display mounted and lacquered digital content on the front and QR codes which link web addresses on the reverse.',
                'range_flag' => 4,
                'range_start' => null,
                'range_end' => null,
                'image_id' => null,
                'edition_id' => 20,
                'subscription_id' => null,
                'assigned_piece_count' => 1,
                'fluid_piece_count' => 1,
                'collected_piece_count' => 0
            ],
            [
                'id' => 5,
                'created' => '2016-03-08 02:18:03',
                'modified' => '2016-03-08 02:18:03',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '16 x 20 Box',
                'description' => '16 x 20 drop-spine box with linen sides, brown iris case.
Leather label (honey calf) on front with title stamped in black.
Title stamped in black of cloth of spine',
                'range_flag' => 5,
                'range_start' => null,
                'range_end' => null,
                'image_id' => null,
                'edition_id' => 7,
                'subscription_id' => null,
                'assigned_piece_count' => 10,
                'fluid_piece_count' => 10,
                'collected_piece_count' => 0
            ],
            [
                'id' => 6,
                'created' => '2016-03-09 21:24:24',
                'modified' => '2016-10-05 03:08:55',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Hand bound',
                'description' => '6.5" x 5.25" 20 page offset printed book.
Pamphlet stitched, bound in cloth over board covers.',
                'range_flag' => 6,
                'range_start' => null,
                'range_end' => null,
                'image_id' => 16,
                'edition_id' => 10,
                'subscription_id' => null,
                'assigned_piece_count' => 100,
                'fluid_piece_count' => 68,
                'collected_piece_count' => 26
            ],
            [
                'id' => 7,
                'created' => '2016-03-10 19:03:10',
                'modified' => '2016-03-10 19:03:10',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book. Pamphlet stitched, in a paper cover with sleaves',
                'range_flag' => 7,
                'range_start' => null,
                'range_end' => null,
                'image_id' => null,
                'edition_id' => 14,
                'subscription_id' => null,
                'assigned_piece_count' => 300,
                'fluid_piece_count' => 219,
                'collected_piece_count' => 7
            ],
            [
                'id' => 8,
                'created' => '2016-10-05 19:49:43',
                'modified' => '2016-10-05 19:49:43',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book unbound page sets',
                'range_flag' => 8,
                'range_start' => null,
                'range_end' => null,
                'image_id' => null,
                'edition_id' => 28,
                'subscription_id' => null,
                'assigned_piece_count' => 400,
                'fluid_piece_count' => 129,
                'collected_piece_count' => 1
            ],
            [
                'id' => 9,
                'created' => '2016-08-28 01:02:45',
                'modified' => '2016-09-22 15:34:10',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'description' => 'Digital printing on Mohawk Superfine; sewn signatures bound in black goat skin. Pages have graphite edging. Covers are gold foil stamped. 
',
                'range_flag' => 9,
                'range_start' => null,
                'range_end' => null,
                'image_id' => 14,
                'edition_id' => 21,
                'subscription_id' => null,
                'assigned_piece_count' => 15,
                'fluid_piece_count' => 7,
                'collected_piece_count' => 6
            ],
            [
                'id' => 10,
                'created' => '2018-04-30 15:46:06',
                'modified' => '2018-04-30 15:46:06',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Boxed',
                'description' => '',
                'range_flag' => 10,
                'range_start' => null,
                'range_end' => null,
                'image_id' => null,
                'edition_id' => 36,
                'subscription_id' => null,
                'assigned_piece_count' => 3,
                'fluid_piece_count' => 3,
                'collected_piece_count' => 0
            ],
            [
                'id' => 11,
                'created' => '2016-06-02 15:19:21',
                'modified' => '2016-06-02 15:19:21',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '',
                'description' => '',
                'range_flag' => 11,
                'range_start' => null,
                'range_end' => null,
                'image_id' => null,
                'edition_id' => 18,
                'subscription_id' => null,
                'assigned_piece_count' => 50,
                'fluid_piece_count' => 49,
                'collected_piece_count' => 0
            ],
            [
                'id' => 12,
                'created' => '2018-04-06 03:05:40',
                'modified' => '2018-04-06 03:05:40',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '16 x 20',
                'description' => 'Loose silver gelatin prints
',
                'range_flag' => 12,
                'range_start' => null,
                'range_end' => null,
                'image_id' => null,
                'edition_id' => 35,
                'subscription_id' => null,
                'assigned_piece_count' => 5,
                'fluid_piece_count' => 5,
                'collected_piece_count' => 0
            ],
            [
                'id' => 13,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-08 22:19:31',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '8x10 ',
                'description' => 'Matted, framed, silver gelatin prints.',
                'range_flag' => 13,
                'range_start' => null,
                'range_end' => null,
                'image_id' => null,
                'edition_id' => 35,
                'subscription_id' => null,
                'assigned_piece_count' => 5,
                'fluid_piece_count' => 4,
                'collected_piece_count' => 0
            ],
        ];
        parent::init();
    }
}

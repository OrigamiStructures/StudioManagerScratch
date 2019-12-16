<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * EditionsFormatsFixture
 *
 */
class EditionsFormatsFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'editions_formats';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'user_id' => ['type' => 'uuid', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'format_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'edition_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'assigned_piece_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'fluid_piece_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'collected_piece_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'title' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'description' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null],
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
                'format_id' => 1,
                'edition_id' => 5,
                'assigned_piece_count' => 1,
                'fluid_piece_count' => 1,
                'collected_piece_count' => 0,
                'title' => null,
                'description' => 'Watercolor 6 x 15"'
            ],
            [
                'id' => 2,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 2,
                'edition_id' => 8,
                'assigned_piece_count' => 150,
                'fluid_piece_count' => 149,
                'collected_piece_count' => 1,
                'title' => 'Card Bound',
                'description' => 'Digital output with cloth-covered card stock covers'
            ],
            [
                'id' => 3,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 3,
                'edition_id' => 6,
                'assigned_piece_count' => 15,
                'fluid_piece_count' => 0,
                'collected_piece_count' => 6,
                'title' => 'Mini Box',
                'description' => 'Paper covered container with 4 trays. Trays display mounted and lacquered digital content on the front and QR codes which link web addresses on the reverse'
            ],
            [
                'id' => 4,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 4,
                'edition_id' => 20,
                'assigned_piece_count' => 1,
                'fluid_piece_count' => 1,
                'collected_piece_count' => 0,
                'title' => '',
                'description' => 'Prototype made while developing edition details.
Paper covered container with 4 trays. Trays display mounted and lacquered digital content on the front and QR codes which link web addresses on the reverse.'
            ],
            [
                'id' => 5,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 5,
                'edition_id' => 7,
                'assigned_piece_count' => 10,
                'fluid_piece_count' => 10,
                'collected_piece_count' => 0,
                'title' => '16 x 20 Box',
                'description' => '16 x 20 drop-spine box with linen sides, brown iris case.
Leather label (honey calf) on front with title stamped in black.
Title stamped in black of cloth of spine'
            ],
            [
                'id' => 6,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 6,
                'edition_id' => 10,
                'assigned_piece_count' => 100,
                'fluid_piece_count' => 68,
                'collected_piece_count' => 26,
                'title' => 'Hand bound',
                'description' => '6.5" x 5.25" 20 page offset printed book.
Pamphlet stitched, bound in cloth over board covers.'
            ],
            [
                'id' => 7,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 7,
                'edition_id' => 17,
                'assigned_piece_count' => 300,
                'fluid_piece_count' => 224,
                'collected_piece_count' => 3,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book. Pamphlet stitched, in a paper cover with sleaves'
            ],
            [
                'id' => 8,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 8,
                'edition_id' => 28,
                'assigned_piece_count' => 400,
                'fluid_piece_count' => 129,
                'collected_piece_count' => 1,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book unbound page sets'
            ],
            [
                'id' => 9,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 6,
                'edition_id' => 11,
                'assigned_piece_count' => 100,
                'fluid_piece_count' => 62,
                'collected_piece_count' => 23,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book.
Pamphlet stitched, bound in cloth over board covers.'
            ],
            [
                'id' => 10,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 7,
                'edition_id' => 16,
                'assigned_piece_count' => 300,
                'fluid_piece_count' => 161,
                'collected_piece_count' => 3,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book. Pamphlet stitched, in a paper cover with sleaves'
            ],
            [
                'id' => 11,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 8,
                'edition_id' => 29,
                'assigned_piece_count' => 400,
                'fluid_piece_count' => 375,
                'collected_piece_count' => 1,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book pages'
            ],
            [
                'id' => 12,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 6,
                'edition_id' => 12,
                'assigned_piece_count' => 100,
                'fluid_piece_count' => 73,
                'collected_piece_count' => 22,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book. Pamphlet stitched, bound in cloth over board covers.'
            ],
            [
                'id' => 13,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 7,
                'edition_id' => 15,
                'assigned_piece_count' => 300,
                'fluid_piece_count' => 257,
                'collected_piece_count' => 3,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book. Pamphlet stitched, in a paper cover with sleaves'
            ],
            [
                'id' => 14,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 8,
                'edition_id' => 31,
                'assigned_piece_count' => 400,
                'fluid_piece_count' => 375,
                'collected_piece_count' => 1,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book pages'
            ],
            [
                'id' => 15,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 6,
                'edition_id' => 13,
                'assigned_piece_count' => 100,
                'fluid_piece_count' => 74,
                'collected_piece_count' => 22,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book. Pamphlet stitched, bound in cloth over board covers.'
            ],
            [
                'id' => 16,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 7,
                'edition_id' => 14,
                'assigned_piece_count' => 300,
                'fluid_piece_count' => 219,
                'collected_piece_count' => 7,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book. Pamphlet stitched, in a paper cover with sleaves'
            ],
            [
                'id' => 17,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 8,
                'edition_id' => 30,
                'assigned_piece_count' => 400,
                'fluid_piece_count' => 364,
                'collected_piece_count' => 1,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book pages'
            ],
            [
                'id' => 18,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 11,
                'edition_id' => 18,
                'assigned_piece_count' => 50,
                'fluid_piece_count' => 49,
                'collected_piece_count' => 0,
                'title' => '',
                'description' => ''
            ],
            [
                'id' => 19,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 9,
                'edition_id' => 21,
                'assigned_piece_count' => 15,
                'fluid_piece_count' => 7,
                'collected_piece_count' => 6,
                'title' => '',
                'description' => 'Digital printing on Mohawk Superfine; sewn signatures bound in black goat skin. Pages have graphite edging. Covers are gold foil stamped.
'
            ],
            [
                'id' => 20,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 11,
                'edition_id' => 22,
                'assigned_piece_count' => 3,
                'fluid_piece_count' => 1,
                'collected_piece_count' => 1,
                'title' => '',
                'description' => ''
            ],
            [
                'id' => 21,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 6,
                'edition_id' => 24,
                'assigned_piece_count' => 100,
                'fluid_piece_count' => 71,
                'collected_piece_count' => 21,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book. Pamphlet stitched, bound in cloth over board covers.'
            ],
            [
                'id' => 22,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 7,
                'edition_id' => 26,
                'assigned_piece_count' => 300,
                'fluid_piece_count' => 242,
                'collected_piece_count' => 4,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book. Pamphlet stitched, in a paper cover with sleaves'
            ],
            [
                'id' => 23,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 8,
                'edition_id' => 32,
                'assigned_piece_count' => 400,
                'fluid_piece_count' => 375,
                'collected_piece_count' => 1,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book pages'
            ],
            [
                'id' => 24,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 6,
                'edition_id' => 25,
                'assigned_piece_count' => 100,
                'fluid_piece_count' => 70,
                'collected_piece_count' => 22,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book. Pamphlet stitched, bound in cloth over board covers.'
            ],
            [
                'id' => 25,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 7,
                'edition_id' => 27,
                'assigned_piece_count' => 300,
                'fluid_piece_count' => 239,
                'collected_piece_count' => 6,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book. Pamphlet stitched, in a paper cover with sleaves'
            ],
            [
                'id' => 26,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 8,
                'edition_id' => 33,
                'assigned_piece_count' => 400,
                'fluid_piece_count' => 375,
                'collected_piece_count' => 1,
                'title' => '',
                'description' => '6.5" x 5.25" 20 page offset printed book pages'
            ],
            [
                'id' => 27,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 11,
                'edition_id' => 34,
                'assigned_piece_count' => 50,
                'fluid_piece_count' => 50,
                'collected_piece_count' => 0,
                'title' => '',
                'description' => ''
            ],
            [
                'id' => 28,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 13,
                'edition_id' => 35,
                'assigned_piece_count' => 5,
                'fluid_piece_count' => 4,
                'collected_piece_count' => 0,
                'title' => '8x10 ',
                'description' => 'Matted, framed, silver gelatin prints.'
            ],
            [
                'id' => 29,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 12,
                'edition_id' => 35,
                'assigned_piece_count' => 5,
                'fluid_piece_count' => 5,
                'collected_piece_count' => 0,
                'title' => '16 x 20',
                'description' => 'Loose silver gelatin prints
'
            ],
            [
                'id' => 30,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'format_id' => 10,
                'edition_id' => 36,
                'assigned_piece_count' => 3,
                'fluid_piece_count' => 3,
                'collected_piece_count' => 0,
                'title' => 'Boxed',
                'description' => ''
            ],
        ];
        parent::init();
    }
}

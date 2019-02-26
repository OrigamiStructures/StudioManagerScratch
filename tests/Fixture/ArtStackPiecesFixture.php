<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ArtStackPieceFixture
 *
 */
class ArtStackPiecesFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'pieces';

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
        'number' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'NULL if un-numbered, or the piece number', 'precision' => null, 'autoIncrement' => null],
        'quantity' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '1', 'comment' => 'qty of un-numbered pieces. 1 if piece is numbered', 'precision' => null, 'autoIncrement' => null],
        'made' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null],
        'edition_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'must always have a value', 'precision' => null, 'autoIncrement' => null],
        'format_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'a value here means \'assigned\'. may be null', 'precision' => null, 'autoIncrement' => null],
        'disposition_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => 'number of dispositions linked', 'precision' => null, 'autoIncrement' => null],
        'collected' => ['type' => 'integer', 'length' => 1, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => 'counter cache boolean', 'precision' => null, 'autoIncrement' => null],
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
                'id' => 956,
                'created' => '2018-04-08 03:32:30',
                'modified' => '2018-05-14 16:12:56',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 8,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 957,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-05-14 16:12:56',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 7,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 1
            ],
            [
                'id' => 958,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-05-14 23:54:46',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 6,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 1
            ],
            [
                'id' => 959,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-05-14 16:12:56',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 9,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 960,
                'created' => '2018-10-27 16:18:47',
                'modified' => '2018-10-27 16:18:47',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 10,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
    //start chunk 1
            [
                'id' => 961,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-05-15 00:03:40',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 2,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => 36,
                'disposition_count' => 1,
                'collected' => 1
            ],
            [
                'id' => 962,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-05-14 23:49:07',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 5,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => 36,
                'disposition_count' => 0,
                'collected' => 1
            ],
            [
                'id' => 963,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-05-14 16:12:56',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 3,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => 36,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 964,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-05-14 16:12:56',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 4,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => 36,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 965,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-05-15 00:03:40',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 1,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => 36,
                'disposition_count' => 0,
                'collected' => 0
            ],
    //end chunk 1
            [
                'id' => 966,
                'created' => '2018-04-08 02:29:18',
                'modified' => '2018-04-08 02:29:18',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 11,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 967,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-08 02:29:18',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 12,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 968,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-08 02:29:18',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 13,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 969,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-08 02:29:18',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 14,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 970,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-08 02:29:18',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 15,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 971,
                'created' => '2018-10-27 16:18:47',
                'modified' => '2018-10-27 16:18:47',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 16,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => 37,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 972,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-07 13:57:52',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 17,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => 37,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 973,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-07 13:57:52',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 18,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => 37,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 974,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-07 13:57:52',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 19,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => 37,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 975,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-07 13:57:52',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 20,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => 37,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 976,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 21,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 977,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 22,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 978,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 23,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 979,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 24,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 980,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 25,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 981,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 26,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 982,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 27,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 983,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 28,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 984,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 29,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 985,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 30,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 986,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 31,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 987,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 32,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 988,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 33,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 989,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 34,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 990,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 35,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 991,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 36,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 992,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:35',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 37,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 993,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 38,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 994,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 39,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 995,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 40,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 996,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 41,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 997,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 42,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 998,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 43,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 999,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 44,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 1000,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 45,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 1001,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 46,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 1002,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 47,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 1003,
                'created' => '2018-04-06 03:04:49',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 48,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 1004,
                'created' => '2018-04-06 03:04:50',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 49,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 1005,
                'created' => '2018-04-06 03:04:50',
                'modified' => '2018-04-06 05:41:36',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 50,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 35,
                'format_id' => null,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 1006,
                'created' => '2018-04-30 15:46:06',
                'modified' => '2018-04-30 15:46:06',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 1,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 36,
                'format_id' => 38,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 1007,
                'created' => '2018-04-30 15:46:06',
                'modified' => '2018-04-30 15:46:06',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 2,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 36,
                'format_id' => 38,
                'disposition_count' => 0,
                'collected' => 0
            ],
            [
                'id' => 1008,
                'created' => '2018-04-30 15:46:06',
                'modified' => '2018-04-30 15:46:06',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'number' => 3,
                'quantity' => 1,
                'made' => false,
                'edition_id' => 36,
                'format_id' => 38,
                'disposition_count' => 0,
                'collected' => 0
            ],
        ];
        parent::init();
    }
}

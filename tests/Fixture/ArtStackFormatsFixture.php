<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ArtStackFormatFixture
 *
 */
class ArtStackFormatsFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'formats';

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
                'id' => 36,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-04-08 22:19:31',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '8x10 ',
                'description' => 'Matted, framed, silver gelatin prints.',
                'range_flag' => null,
                'range_start' => null,
                'range_end' => null,
                'image_id' => null,
                'edition_id' => 35,
                'subscription_id' => null,
                'assigned_piece_count' => 0,
                'fluid_piece_count' => 4,
                'collected_piece_count' => 0
            ],
            [
                'id' => 37,
                'created' => '2018-04-06 03:05:40',
                'modified' => '2018-04-06 03:05:40',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => '16 x 20',
                'description' => 'Loose silver gelatin prints
',
                'range_flag' => null,
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
                'id' => 38,
                'created' => '2018-04-30 15:46:06',
                'modified' => '2018-04-30 15:46:06',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'title' => 'Boxed',
                'description' => '',
                'range_flag' => null,
                'range_start' => null,
                'range_end' => null,
                'image_id' => null,
                'edition_id' => 36,
                'subscription_id' => null,
                'assigned_piece_count' => 3,
                'fluid_piece_count' => 3,
                'collected_piece_count' => 0
            ],
        ];
        parent::init();
    }
}

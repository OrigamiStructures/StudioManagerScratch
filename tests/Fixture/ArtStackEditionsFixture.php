<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ArtStackEditionFixture
 *
 */
class ArtStackEditionsFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'editions';

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

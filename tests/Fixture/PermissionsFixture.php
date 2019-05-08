<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PermissionsFixture
 *
 */
class PermissionsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'layer_name' => ['type' => 'string', 'length' => 30, 'null' => false, 'default' => '', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'layer_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'user_id' => ['type' => 'string', 'length' => 36, 'null' => false, 'default' => '', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'manifest_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'edit' => ['type' => 'integer', 'length' => 1, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
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
                'layer_name' => 'artwork',
                'layer_id' => 0,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'manifest_id' => 1,
                'edit' => 1
            ],
            [
                'id' => 2,
                'layer_name' => 'edition',
                'layer_id' => 6,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'manifest_id' => 3,
                'edit' => 0
            ],
            [
                'id' => 3,
                'layer_name' => 'artwork',
                'layer_id' => 4,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'manifest_id' => 3,
                'edit' => 0
            ],
        ];
        parent::init();
    }
}

<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ManifestsFixture
 *
 */
class ManifestsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'member_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'id of Member record that is an artist', 'precision' => null, 'autoIncrement' => null],
        'user_id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => '', 'comment' => 'id of the registered user that controls this \'access/rights\' record', 'precision' => null],
        'manager_id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => '', 'comment' => 'id of Member record that is the manager', 'precision' => null],
        'supervisor_id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => '', 'comment' => 'id of Member record that is the supervisor/issuer', 'precision' => null],
        'publish_manager' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => 'whether other managers can see this manager', 'precision' => null],
        'publish_manager_contact' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => 'when published, include primary contact/address visibility', 'precision' => null],
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
                'member_id' => 1,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'manager_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'supervisor_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'publish_manager' => true,
                'publish_manager_contact' => true
            ],
            [
                'id' => 2,
                'member_id' => 2,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'manager_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'supervisor_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'publish_manager' => false,
                'publish_manager_contact' => false
            ],
            [
                'id' => 3,
                'member_id' => 1,
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'manager_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'supervisor_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'publish_manager' => false,
                'publish_manager_contact' => false
            ],
            [
                'id' => 4,
                'member_id' => 75,
                'user_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'manager_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'supervisor_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'publish_manager' => true,
                'publish_manager_contact' => true
            ],
            [
                'id' => 5,
                'member_id' => 75,
                'user_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'manager_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'supervisor_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'publish_manager' => false,
                'publish_manager_contact' => false
            ],
        ];
        parent::init();
    }
}

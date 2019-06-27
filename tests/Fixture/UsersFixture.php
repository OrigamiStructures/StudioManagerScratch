<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 *
 */
class UsersFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'management_token' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => '', 'comment' => '', 'precision' => null],
        'username' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'email' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'first_name' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'last_name' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'token' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'token_expires' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'api_token' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'activation_date' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'tos_date' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'active' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null],
        'is_superuser' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null],
        'role' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => 'user', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'artist_id' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'member_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
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
                'id' => '008ab31c-124d-4e15-a4e1-45fccd7becac',
                'management_token' => '008ab31c-124d-4e15-a4e1-45fccd7becac',
                'username' => 'jason',
                'email' => 'jason@curlymedia.com',
                'password' => '$2y$10$jekmBBtzjM7zzs6TBH6dnup.uNi1sU2JLtlyvbkacIxe6jm/xwuUS',
                'first_name' => 'Jason',
                'last_name' => 'Tempestini',
                'token' => null,
                'token_expires' => null,
                'api_token' => null,
                'activation_date' => '2016-01-06 21:07:14',
                'tos_date' => '2016-01-06 21:06:27',
                'active' => true,
                'is_superuser' => true,
                'role' => 'user',
                'created' => '2016-01-06 21:06:27',
                'modified' => '2016-08-23 03:35:59',
                'artist_id' => '008ab31c-124d-4e15-a4e1-45fccd7becac',
                'member_id' => null
            ],
            [
                'id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'management_token' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'username' => 'leonardo',
                'email' => 'horseman@dreamingmind.com',
                'password' => '$2y$10$nxaMY7mez6NMPVTXWT5f2.e8shSYLES9Ck5b0WmFyyw3Bupy0wzIG',
                'first_name' => 'Luis',
                'last_name' => 'Delgado',
                'token' => null,
                'token_expires' => null,
                'api_token' => null,
                'activation_date' => '2016-02-11 15:24:13',
                'tos_date' => '2016-02-11 15:23:46',
                'active' => true,
                'is_superuser' => false,
                'role' => 'user',
                'created' => '2016-02-11 15:23:46',
                'modified' => '2016-02-14 19:12:56',
                'artist_id' => '708cfc57-1162-4c5b-9092-42c25da131a9',
                'member_id' => 75
            ],
            [
                'id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'management_token' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'username' => 'don',
                'email' => 'ddrake@dreamingmind.com',
                'password' => '$2y$10$1/eIptEk18zwp.QGIWPVr.VaqM66Bfhk7H7Vf3z6CN.IR3r9uMSLS',
                'first_name' => 'Don',
                'last_name' => 'Drake',
                'token' => '45fd1708fed548568686fbea5d717464',
                'token_expires' => '2018-08-28 05:34:36',
                'api_token' => null,
                'activation_date' => '2016-01-07 22:29:42',
                'tos_date' => '2016-01-08 21:17:35',
                'active' => true,
                'is_superuser' => false,
                'role' => 'user',
                'created' => '2016-01-06 21:17:35',
                'modified' => '2019-06-18 19:46:20',
                'artist_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 1
            ],
        ];
        parent::init();
    }
}

<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContactsFixture
 *
 */
class ContactsFixture extends TestFixture
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
        'member_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'label' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'data' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'primary_contact' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
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
     * Records
     *
     * @var array
     */
public $records = [
        [
            'id' => 1,
            'user_id' => 'a77c54a2-da45-4b52-9960-efbbd91acce6',
            'member_id' => 1,
            'label' => 'email',
            'data' => 'ddrake@dreamingmind.com',
			'primary_contact' => 1
        ],
        [
            'id' => 2,
            'user_id' => 'a77c54a2-da45-4b52-9960-efbbd91acce6',
            'member_id' => 1,
            'label' => 'phone',
            'data' => '5104159987',
			'primary_contact' => 0
        ],
        [
            'id' => 3,
            'user_id' => 'a77c54a2-da45-4b52-9960-efbbd91acce6',
            'member_id' => 1,
            'label' => 'url',
            'data' => 'dreamingmind.com',
			'primary_contact' => null
        ],
        [
            'id' => 4,
            'user_id' => 'a77c54a2-da45-4b52-9960-efbbd91acce6',
            'member_id' => 1,
            'label' => 'curl-addr',
            'data' => 'dev.amp-fg.com/JSONStatusRequest',
			'primary_contact' => 0
        ],
        [
            'id' => 5,
            'user_id' => 'a77c54a2-da45-4b52-9960-efbbd91acce6',
            'member_id' => 2,
            'label' => 'url',
            'data' => 'dreamingmind.com',
			'primary_contact' => null
        ],
    ];	

}

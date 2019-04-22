<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * DispositionsFixture
 *
 */
class DispositionsFixture extends TestFixture
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
        'member_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'The source member', 'precision' => null, 'autoIncrement' => null],
        'address_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'The source address', 'precision' => null, 'autoIncrement' => null],
        'start_date' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'end_date' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'type' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'the underlying category of disposition (loan, transfer, etc)', 'precision' => null, 'fixed' => null],
        'label' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'the human name for this dispo of this type (sold, show, etc)', 'precision' => null, 'fixed' => null],
        'name' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'user specified name of the disposition event', 'precision' => null, 'fixed' => null],
        'complete' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => 'has shipping or execution been satisfied', 'precision' => null],
        'disposition_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'the loan-type disposition this extends', 'precision' => null, 'autoIncrement' => null],
        'first_name' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'the member first name', 'precision' => null, 'fixed' => null],
        'last_name' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'the member last name', 'precision' => null, 'fixed' => null],
        'address1' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'copies of all the address data attached when the dispo was created', 'precision' => null, 'fixed' => null],
        'address2' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'address3' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'city' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'state' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'zip' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'country' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
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
                'id' => 122,
                'created' => '2016-10-10 18:39:47',
                'modified' => '2016-10-10 18:39:47',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 73,
                'address_id' => null,
                'start_date' => '2016-10-10 00:00:00',
                'end_date' => '2016-10-10 00:00:00',
                'type' => 'transfer',
                'label' => 'Sale',
                'name' => '2015, 2016 OnePoem Subscription Fulfillment',
                'complete' => true,
                'disposition_id' => null,
                'first_name' => 'Sheila',
                'last_name' => 'Botein',
                'address1' => '',
                'address2' => '',
                'address3' => '',
                'city' => '',
                'state' => '',
                'zip' => '',
                'country' => ''
            ],
            [
                'id' => 123,
                'created' => '2016-10-10 21:36:20',
                'modified' => '2016-10-10 21:36:20',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 33,
                'address_id' => null,
                'start_date' => '2016-10-10 00:00:00',
                'end_date' => '2016-10-10 00:00:00',
                'type' => 'transfer',
                'label' => 'Gift',
                'name' => 'Kate\'s Gift',
                'complete' => true,
                'disposition_id' => null,
                'first_name' => 'Sharon',
                'last_name' => 'Shoemaker',
                'address1' => '',
                'address2' => '',
                'address3' => '',
                'city' => '',
                'state' => '',
                'zip' => '',
                'country' => ''
            ],
            [
                'id' => 124,
                'created' => '2016-10-10 21:39:29',
                'modified' => '2016-10-10 21:39:29',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 1,
                'address_id' => null,
                'start_date' => '2016-10-10 00:00:00',
                'end_date' => '2016-10-10 00:00:00',
                'type' => 'storage',
                'label' => 'Storage',
                'name' => 'Dreaming Mind Studio',
                'complete' => true,
                'disposition_id' => null,
                'first_name' => 'Don',
                'last_name' => 'Drake',
                'address1' => '5664 Sunridge Court',
                'address2' => '',
                'address3' => '',
                'city' => 'Castro Valley',
                'state' => 'CA',
                'zip' => '94552',
                'country' => 'USA'
            ],
            [
                'id' => 125,
                'created' => '2016-10-10 22:27:38',
                'modified' => '2016-10-10 22:27:38',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 8,
                'address_id' => null,
                'start_date' => '2016-10-10 00:00:00',
                'end_date' => '2017-01-31 00:00:00',
                'type' => 'loan',
                'label' => 'Loan',
                'name' => 'Example Copies',
                'complete' => false,
                'disposition_id' => 100,
                'first_name' => 'Kate',
                'last_name' => 'Jordahl',
                'address1' => '463 Lawton Pl',
                'address2' => '',
                'address3' => '',
                'city' => 'Hayward',
                'state' => 'CA',
                'zip' => '94544',
                'country' => 'USA'
            ],
            [
                'id' => 126,
                'created' => '2016-10-10 22:36:40',
                'modified' => '2016-10-10 22:36:40',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 8,
                'address_id' => null,
                'start_date' => '2016-10-10 00:00:00',
                'end_date' => '2017-01-01 00:00:00',
                'type' => 'loan',
                'label' => 'Loan',
                'name' => 'Example Copies',
                'complete' => false,
                'disposition_id' => 100,
                'first_name' => 'Kate',
                'last_name' => 'Jordahl',
                'address1' => '463 Lawton Pl',
                'address2' => '',
                'address3' => '',
                'city' => 'Hayward',
                'state' => 'CA',
                'zip' => '94544',
                'country' => 'USA'
            ],
            [
                'id' => 127,
                'created' => '2016-10-12 18:32:41',
                'modified' => '2016-10-12 18:32:41',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 1,
                'address_id' => 1,
                'start_date' => '2016-10-16 00:00:00',
                'end_date' => '2016-10-17 00:00:00',
                'type' => 'loan',
                'label' => 'Loan',
                'name' => '2016 Book Arts Jam',
                'complete' => false,
                'disposition_id' => 100,
                'first_name' => 'Don',
                'last_name' => 'Drake',
                'address1' => '5664 Sunridge Court',
                'address2' => '',
                'address3' => '',
                'city' => 'Castro Valley',
                'state' => 'CA',
                'zip' => '94552',
                'country' => 'USA'
            ],
            [
                'id' => 129,
                'created' => '2016-10-17 19:51:13',
                'modified' => '2016-10-17 19:51:13',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 1,
                'address_id' => 1,
                'start_date' => '2016-10-17 00:00:00',
                'end_date' => '2016-10-17 00:00:00',
                'type' => 'storage',
                'label' => 'Storage',
                'name' => 'Dreaming Mind Studio',
                'complete' => true,
                'disposition_id' => null,
                'first_name' => 'Don',
                'last_name' => 'Drake',
                'address1' => '5664 Sunridge Court',
                'address2' => '',
                'address3' => '',
                'city' => 'Castro Valley',
                'state' => 'CA',
                'zip' => '94552',
                'country' => 'USA'
            ],
            [
                'id' => 131,
                'created' => '2018-04-08 22:23:51',
                'modified' => '2018-04-08 22:23:51',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 2,
                'address_id' => 2,
                'start_date' => '2018-04-08 00:00:00',
                'end_date' => '2018-04-08 00:00:00',
                'type' => 'storage',
                'label' => 'Storage',
                'name' => '',
                'complete' => true,
                'disposition_id' => null,
                'first_name' => 'Gail',
                'last_name' => 'Drake',
                'address1' => '5664 Sunridge Court',
                'address2' => '',
                'address3' => '',
                'city' => 'Castro Valley',
                'state' => 'CA',
                'zip' => '94552',
                'country' => 'USA'
            ],
            [
                'id' => 132,
                'created' => '2019-01-28 21:30:15',
                'modified' => '2019-01-28 21:30:15',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'member_id' => 52,
                'address_id' => 54,
                'start_date' => '2019-01-28 00:00:00',
                'end_date' => '2019-01-28 00:00:00',
                'type' => 'transfer',
                'label' => 'Gift',
                'name' => 'Don Drake',
                'complete' => true,
                'disposition_id' => null,
                'first_name' => 'Adobe Gallery',
                'last_name' => 'Adobe Gallery',
                'address1' => '20395 San Miguel Avenue',
                'address2' => '',
                'address3' => '',
                'city' => 'Castro Valley',
                'state' => 'CA',
                'zip' => '',
                'country' => ''
            ],
        ];
        parent::init();
    }
}

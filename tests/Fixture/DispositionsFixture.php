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
        'member_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'address_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'start_date' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'end_date' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'type' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'comment' => 'the underlying category of disposition (loan, transfer, etc)', 'precision' => null, 'fixed' => null],
        'label' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'comment' => 'the human name for this dispo of this type (sold, show, etc)', 'precision' => null, 'fixed' => null],
        'complete' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => 'has shipping or execution been satisfied', 'precision' => null],
        'disposition_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'the loan-type disposition this extends', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'comment' => 'user specified name of the disposition event', 'precision' => null, 'fixed' => null],
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
            'created' => '2016-03-04 22:33:08',
            'modified' => '2016-03-04 22:33:08',
            'user_id' => '7fc534db-d5ec-4da8-bbe5-55ab9f2a3c96',
            'member_id' => 1,
            'address_id' => 1,
            'start_date' => '2016-03-04',
            'end_date' => '2016-03-04',
            'type' => 'Lorem ipsum dolor sit amet',
            'label' => 'Lorem ipsum dolor sit amet',
            'complete' => 1,
            'disposition_id' => 1,
            'name' => 'Lorem ipsum dolor sit amet'
        ],
    ];
}

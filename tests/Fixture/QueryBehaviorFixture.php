<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PiecesFixture
 *
 */
class QueryBehaviorFixture extends TestFixture
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
        'number' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'title' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        ['created' => '2015-12-10 21:31:29',
            'number' => 1,
            'title' => 'The First Title',],
        ['created' => '2015-12-11 21:31:29',
            'number' => 2,
            'title' => 'The Second Title',],
        ['created' => '2015-12-12 21:31:29',
            'number' => 3,
            'title' => 'the third title',],
        ['created' => '2015-12-13 21:31:29',
            'number' => 4,
            'title' => 'the fourth title',],
        ['created' => '2015-12-14 21:31:29',
            'number' => 5,
            'title' => 'The Fifth Title',],
        ['created' => '2015-12-15 21:31:29',
            'number' => 6,
            'title' => 'The Sixth Title',],
        ['created' => '2015-12-16 21:31:29',
            'number' => 7,
            'title' => 'The seventh Title',],
    ];
}

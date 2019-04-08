<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * DispositionsPiecesFixture
 *
 */
class DispositionsPiecesFixture extends TestFixture
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
        'disposition_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'piece_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'complete' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
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
                'id' => 12,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 8,
                'piece_id' => 71,
                'complete' => null
            ],
            [
                'id' => 13,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 8,
                'piece_id' => 72,
                'complete' => null
            ],
            [
                'id' => 14,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 8,
                'piece_id' => 445,
                'complete' => null
            ],
            [
                'id' => 15,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 8,
                'piece_id' => 121,
                'complete' => null
            ],
            [
                'id' => 16,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 8,
                'piece_id' => 122,
                'complete' => null
            ],
            [
                'id' => 17,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 8,
                'piece_id' => 446,
                'complete' => null
            ],
            [
                'id' => 18,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 8,
                'piece_id' => 271,
                'complete' => null
            ],
            [
                'id' => 19,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 8,
                'piece_id' => 272,
                'complete' => null
            ],
            [
                'id' => 20,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 8,
                'piece_id' => 447,
                'complete' => null
            ],
            [
                'id' => 21,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 8,
                'piece_id' => 371,
                'complete' => null
            ],
            [
                'id' => 22,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 8,
                'piece_id' => 372,
                'complete' => null
            ],
            [
                'id' => 23,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 8,
                'piece_id' => 448,
                'complete' => null
            ],
            [
                'id' => 24,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 9,
                'piece_id' => 21,
                'complete' => null
            ],
            [
                'id' => 27,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 11,
                'piece_id' => 463,
                'complete' => null
            ],
            [
                'id' => 28,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 12,
                'piece_id' => 510,
                'complete' => null
            ],
            [
                'id' => 29,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 13,
                'piece_id' => 511,
                'complete' => null
            ],
            [
                'id' => 31,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 15,
                'piece_id' => 512,
                'complete' => null
            ],
            [
                'id' => 32,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 16,
                'piece_id' => 513,
                'complete' => null
            ],
            [
                'id' => 33,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 17,
                'piece_id' => 514,
                'complete' => null
            ],
            [
                'id' => 34,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 48,
                'piece_id' => 515,
                'complete' => null
            ],
            [
                'id' => 35,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 19,
                'piece_id' => 91,
                'complete' => null
            ],
            [
                'id' => 36,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 19,
                'piece_id' => 41,
                'complete' => null
            ],
            [
                'id' => 37,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 20,
                'piece_id' => 42,
                'complete' => null
            ],
            [
                'id' => 38,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 20,
                'piece_id' => 92,
                'complete' => null
            ],
            [
                'id' => 39,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 21,
                'piece_id' => 43,
                'complete' => null
            ],
            [
                'id' => 40,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 22,
                'piece_id' => 243,
                'complete' => null
            ],
            [
                'id' => 41,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 23,
                'piece_id' => 44,
                'complete' => null
            ],
            [
                'id' => 42,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 23,
                'piece_id' => 94,
                'complete' => null
            ],
            [
                'id' => 43,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 24,
                'piece_id' => 95,
                'complete' => null
            ],
            [
                'id' => 44,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 25,
                'piece_id' => 45,
                'complete' => null
            ],
            [
                'id' => 45,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 26,
                'piece_id' => 46,
                'complete' => null
            ],
            [
                'id' => 46,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 27,
                'piece_id' => 47,
                'complete' => null
            ],
            [
                'id' => 47,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 27,
                'piece_id' => 97,
                'complete' => null
            ],
            [
                'id' => 48,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 28,
                'piece_id' => 98,
                'complete' => null
            ],
            [
                'id' => 49,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 28,
                'piece_id' => 48,
                'complete' => null
            ],
            [
                'id' => 50,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 29,
                'piece_id' => 49,
                'complete' => null
            ],
            [
                'id' => 51,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 29,
                'piece_id' => 99,
                'complete' => null
            ],
            [
                'id' => 52,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 30,
                'piece_id' => 51,
                'complete' => null
            ],
            [
                'id' => 53,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 30,
                'piece_id' => 101,
                'complete' => null
            ],
            [
                'id' => 54,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 31,
                'piece_id' => 52,
                'complete' => null
            ],
            [
                'id' => 55,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 32,
                'piece_id' => 102,
                'complete' => null
            ],
            [
                'id' => 56,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 33,
                'piece_id' => 103,
                'complete' => null
            ],
            [
                'id' => 57,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 33,
                'piece_id' => 53,
                'complete' => null
            ],
            [
                'id' => 58,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 34,
                'piece_id' => 54,
                'complete' => null
            ],
            [
                'id' => 59,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 34,
                'piece_id' => 104,
                'complete' => null
            ],
            [
                'id' => 60,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 35,
                'piece_id' => 105,
                'complete' => null
            ],
            [
                'id' => 61,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 35,
                'piece_id' => 55,
                'complete' => null
            ],
            [
                'id' => 62,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 36,
                'piece_id' => 56,
                'complete' => null
            ],
            [
                'id' => 63,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 36,
                'piece_id' => 106,
                'complete' => null
            ],
            [
                'id' => 64,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 37,
                'piece_id' => 57,
                'complete' => null
            ],
            [
                'id' => 65,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 37,
                'piece_id' => 107,
                'complete' => null
            ],
            [
                'id' => 66,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 38,
                'piece_id' => 58,
                'complete' => null
            ],
            [
                'id' => 67,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 38,
                'piece_id' => 108,
                'complete' => null
            ],
            [
                'id' => 68,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 39,
                'piece_id' => 59,
                'complete' => null
            ],
            [
                'id' => 69,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 40,
                'piece_id' => 61,
                'complete' => null
            ],
            [
                'id' => 70,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 40,
                'piece_id' => 111,
                'complete' => null
            ],
            [
                'id' => 71,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 41,
                'piece_id' => 60,
                'complete' => null
            ],
            [
                'id' => 72,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 42,
                'piece_id' => 63,
                'complete' => null
            ],
            [
                'id' => 73,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 43,
                'piece_id' => 65,
                'complete' => null
            ],
            [
                'id' => 74,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 44,
                'piece_id' => 115,
                'complete' => null
            ],
            [
                'id' => 75,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 45,
                'piece_id' => 161,
                'complete' => null
            ],
            [
                'id' => 76,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 45,
                'piece_id' => 211,
                'complete' => null
            ],
            [
                'id' => 77,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 46,
                'piece_id' => 83,
                'complete' => null
            ],
            [
                'id' => 78,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 46,
                'piece_id' => 133,
                'complete' => null
            ],
            [
                'id' => 79,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 47,
                'piece_id' => 228,
                'complete' => null
            ],
            [
                'id' => 80,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 18,
                'piece_id' => 516,
                'complete' => null
            ],
            [
                'id' => 81,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 49,
                'piece_id' => 526,
                'complete' => null
            ],
            [
                'id' => 82,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 50,
                'piece_id' => 525,
                'complete' => null
            ],
            [
                'id' => 83,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 51,
                'piece_id' => 517,
                'complete' => null
            ],
            [
                'id' => 85,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 53,
                'piece_id' => 22,
                'complete' => null
            ],
            [
                'id' => 86,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 54,
                'piece_id' => 23,
                'complete' => null
            ],
            [
                'id' => 87,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 55,
                'piece_id' => 24,
                'complete' => null
            ],
            [
                'id' => 88,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 56,
                'piece_id' => 25,
                'complete' => null
            ],
            [
                'id' => 89,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 57,
                'piece_id' => 26,
                'complete' => null
            ],
            [
                'id' => 90,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 58,
                'piece_id' => 27,
                'complete' => null
            ],
            [
                'id' => 91,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 58,
                'piece_id' => 501,
                'complete' => null
            ],
            [
                'id' => 92,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 58,
                'piece_id' => 502,
                'complete' => null
            ],
            [
                'id' => 93,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 58,
                'piece_id' => 503,
                'complete' => null
            ],
            [
                'id' => 94,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 58,
                'piece_id' => 504,
                'complete' => null
            ],
            [
                'id' => 95,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 58,
                'piece_id' => 505,
                'complete' => null
            ],
            [
                'id' => 96,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 58,
                'piece_id' => 506,
                'complete' => null
            ],
            [
                'id' => 97,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 58,
                'piece_id' => 507,
                'complete' => null
            ],
            [
                'id' => 98,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 59,
                'piece_id' => 500,
                'complete' => null
            ],
            [
                'id' => 99,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 60,
                'piece_id' => 62,
                'complete' => null
            ],
            [
                'id' => 100,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 60,
                'piece_id' => 100,
                'complete' => null
            ],
            [
                'id' => 101,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 60,
                'piece_id' => 440,
                'complete' => null
            ],
            [
                'id' => 102,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 60,
                'piece_id' => 247,
                'complete' => null
            ],
            [
                'id' => 103,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 60,
                'piece_id' => 727,
                'complete' => null
            ],
            [
                'id' => 104,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 60,
                'piece_id' => 827,
                'complete' => null
            ],
            [
                'id' => 105,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 61,
                'piece_id' => 120,
                'complete' => null
            ],
            [
                'id' => 106,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 80,
                'complete' => null
            ],
            [
                'id' => 107,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 256,
                'complete' => null
            ],
            [
                'id' => 108,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 246,
                'complete' => null
            ],
            [
                'id' => 109,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 244,
                'complete' => null
            ],
            [
                'id' => 110,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 250,
                'complete' => null
            ],
            [
                'id' => 111,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 254,
                'complete' => null
            ],
            [
                'id' => 112,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 112,
                'complete' => null
            ],
            [
                'id' => 113,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 93,
                'complete' => null
            ],
            [
                'id' => 114,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 116,
                'complete' => null
            ],
            [
                'id' => 115,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 109,
                'complete' => null
            ],
            [
                'id' => 116,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 114,
                'complete' => null
            ],
            [
                'id' => 117,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 117,
                'complete' => null
            ],
            [
                'id' => 118,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 119,
                'complete' => null
            ],
            [
                'id' => 119,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 118,
                'complete' => null
            ],
            [
                'id' => 120,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 239,
                'complete' => null
            ],
            [
                'id' => 121,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 240,
                'complete' => null
            ],
            [
                'id' => 122,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 439,
                'complete' => null
            ],
            [
                'id' => 123,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 238,
                'complete' => null
            ],
            [
                'id' => 124,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 62,
                'piece_id' => 438,
                'complete' => null
            ],
            [
                'id' => 125,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 63,
                'piece_id' => 828,
                'complete' => null
            ],
            [
                'id' => 126,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 64,
                'piece_id' => 829,
                'complete' => null
            ],
            [
                'id' => 127,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 64,
                'piece_id' => 831,
                'complete' => null
            ],
            [
                'id' => 128,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 64,
                'piece_id' => 832,
                'complete' => null
            ],
            [
                'id' => 129,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 64,
                'piece_id' => 833,
                'complete' => null
            ],
            [
                'id' => 130,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 64,
                'piece_id' => 835,
                'complete' => null
            ],
            [
                'id' => 131,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 65,
                'piece_id' => 842,
                'complete' => null
            ],
            [
                'id' => 132,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 65,
                'piece_id' => 843,
                'complete' => null
            ],
            [
                'id' => 133,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 65,
                'piece_id' => 844,
                'complete' => null
            ],
            [
                'id' => 134,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 65,
                'piece_id' => 845,
                'complete' => null
            ],
            [
                'id' => 135,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 65,
                'piece_id' => 846,
                'complete' => null
            ],
            [
                'id' => 136,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 65,
                'piece_id' => 847,
                'complete' => null
            ],
            [
                'id' => 137,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 66,
                'piece_id' => 848,
                'complete' => null
            ],
            [
                'id' => 138,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 66,
                'piece_id' => 849,
                'complete' => null
            ],
            [
                'id' => 139,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 66,
                'piece_id' => 850,
                'complete' => null
            ],
            [
                'id' => 140,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 66,
                'piece_id' => 851,
                'complete' => null
            ],
            [
                'id' => 141,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 66,
                'piece_id' => 852,
                'complete' => null
            ],
            [
                'id' => 142,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 66,
                'piece_id' => 853,
                'complete' => null
            ],
            [
                'id' => 143,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 67,
                'piece_id' => 356,
                'complete' => null
            ],
            [
                'id' => 144,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 67,
                'piece_id' => 256,
                'complete' => null
            ],
            [
                'id' => 145,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 68,
                'piece_id' => 311,
                'complete' => null
            ],
            [
                'id' => 146,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 68,
                'piece_id' => 411,
                'complete' => null
            ],
            [
                'id' => 147,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 69,
                'piece_id' => 383,
                'complete' => null
            ],
            [
                'id' => 148,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 69,
                'piece_id' => 283,
                'complete' => null
            ],
            [
                'id' => 149,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 70,
                'piece_id' => 351,
                'complete' => null
            ],
            [
                'id' => 150,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 70,
                'piece_id' => 251,
                'complete' => null
            ],
            [
                'id' => 151,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 71,
                'piece_id' => 242,
                'complete' => null
            ],
            [
                'id' => 152,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 71,
                'piece_id' => 342,
                'complete' => null
            ],
            [
                'id' => 153,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 72,
                'piece_id' => 353,
                'complete' => null
            ],
            [
                'id' => 154,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 72,
                'piece_id' => 253,
                'complete' => null
            ],
            [
                'id' => 155,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 73,
                'piece_id' => 245,
                'complete' => null
            ],
            [
                'id' => 156,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 73,
                'piece_id' => 345,
                'complete' => null
            ],
            [
                'id' => 157,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 74,
                'piece_id' => 341,
                'complete' => null
            ],
            [
                'id' => 158,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 74,
                'piece_id' => 241,
                'complete' => null
            ],
            [
                'id' => 159,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 75,
                'piece_id' => 255,
                'complete' => null
            ],
            [
                'id' => 160,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 75,
                'piece_id' => 355,
                'complete' => null
            ],
            [
                'id' => 161,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 76,
                'piece_id' => 361,
                'complete' => null
            ],
            [
                'id' => 162,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 76,
                'piece_id' => 261,
                'complete' => null
            ],
            [
                'id' => 163,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 77,
                'piece_id' => 249,
                'complete' => null
            ],
            [
                'id' => 164,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 77,
                'piece_id' => 349,
                'complete' => null
            ],
            [
                'id' => 165,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 78,
                'piece_id' => 364,
                'complete' => null
            ],
            [
                'id' => 166,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 78,
                'piece_id' => 264,
                'complete' => null
            ],
            [
                'id' => 167,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 79,
                'piece_id' => 244,
                'complete' => null
            ],
            [
                'id' => 168,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 79,
                'piece_id' => 344,
                'complete' => null
            ],
            [
                'id' => 169,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 80,
                'piece_id' => 348,
                'complete' => null
            ],
            [
                'id' => 170,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 80,
                'piece_id' => 248,
                'complete' => null
            ],
            [
                'id' => 171,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 81,
                'piece_id' => 904,
                'complete' => null
            ],
            [
                'id' => 172,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 82,
                'piece_id' => 905,
                'complete' => null
            ],
            [
                'id' => 173,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 82,
                'piece_id' => 906,
                'complete' => null
            ],
            [
                'id' => 174,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 83,
                'piece_id' => 907,
                'complete' => null
            ],
            [
                'id' => 175,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 83,
                'piece_id' => 908,
                'complete' => null
            ],
            [
                'id' => 176,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 84,
                'piece_id' => 909,
                'complete' => null
            ],
            [
                'id' => 177,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 85,
                'piece_id' => 910,
                'complete' => null
            ],
            [
                'id' => 178,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 86,
                'piece_id' => 911,
                'complete' => null
            ],
            [
                'id' => 179,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 87,
                'piece_id' => 912,
                'complete' => null
            ],
            [
                'id' => 180,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 87,
                'piece_id' => 913,
                'complete' => null
            ],
            [
                'id' => 181,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 88,
                'piece_id' => 265,
                'complete' => null
            ],
            [
                'id' => 182,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 89,
                'piece_id' => 365,
                'complete' => null
            ],
            [
                'id' => 183,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 90,
                'piece_id' => 366,
                'complete' => null
            ],
            [
                'id' => 184,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 90,
                'piece_id' => 66,
                'complete' => null
            ],
            [
                'id' => 185,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 90,
                'piece_id' => 116,
                'complete' => null
            ],
            [
                'id' => 186,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 90,
                'piece_id' => 266,
                'complete' => null
            ],
            [
                'id' => 187,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 96,
                'complete' => null
            ],
            [
                'id' => 188,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 110,
                'complete' => null
            ],
            [
                'id' => 189,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 69,
                'complete' => null
            ],
            [
                'id' => 190,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 68,
                'complete' => null
            ],
            [
                'id' => 191,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 71,
                'complete' => null
            ],
            [
                'id' => 192,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 72,
                'complete' => null
            ],
            [
                'id' => 193,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 121,
                'complete' => null
            ],
            [
                'id' => 194,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 371,
                'complete' => null
            ],
            [
                'id' => 195,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 271,
                'complete' => null
            ],
            [
                'id' => 196,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 272,
                'complete' => null
            ],
            [
                'id' => 197,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 372,
                'complete' => null
            ],
            [
                'id' => 198,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 914,
                'complete' => null
            ],
            [
                'id' => 199,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 915,
                'complete' => null
            ],
            [
                'id' => 200,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 916,
                'complete' => null
            ],
            [
                'id' => 201,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 91,
                'piece_id' => 917,
                'complete' => null
            ],
            [
                'id' => 202,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 92,
                'piece_id' => 628,
                'complete' => null
            ],
            [
                'id' => 203,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 92,
                'piece_id' => 728,
                'complete' => null
            ],
            [
                'id' => 204,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 93,
                'piece_id' => 729,
                'complete' => null
            ],
            [
                'id' => 205,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 93,
                'piece_id' => 629,
                'complete' => null
            ],
            [
                'id' => 206,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 94,
                'piece_id' => 698,
                'complete' => null
            ],
            [
                'id' => 207,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 94,
                'piece_id' => 798,
                'complete' => null
            ],
            [
                'id' => 208,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 95,
                'piece_id' => 770,
                'complete' => null
            ],
            [
                'id' => 209,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 95,
                'piece_id' => 670,
                'complete' => null
            ],
            [
                'id' => 210,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 96,
                'piece_id' => 638,
                'complete' => null
            ],
            [
                'id' => 211,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 96,
                'piece_id' => 738,
                'complete' => null
            ],
            [
                'id' => 212,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 97,
                'piece_id' => 636,
                'complete' => null
            ],
            [
                'id' => 213,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 97,
                'piece_id' => 736,
                'complete' => null
            ],
            [
                'id' => 214,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 98,
                'piece_id' => 732,
                'complete' => null
            ],
            [
                'id' => 215,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 98,
                'piece_id' => 632,
                'complete' => null
            ],
            [
                'id' => 216,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 99,
                'piece_id' => 635,
                'complete' => null
            ],
            [
                'id' => 217,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 99,
                'piece_id' => 735,
                'complete' => null
            ],
            [
                'id' => 218,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 100,
                'piece_id' => 740,
                'complete' => null
            ],
            [
                'id' => 219,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 100,
                'piece_id' => 640,
                'complete' => null
            ],
            [
                'id' => 220,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 101,
                'piece_id' => 642,
                'complete' => null
            ],
            [
                'id' => 221,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 101,
                'piece_id' => 742,
                'complete' => null
            ],
            [
                'id' => 222,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 102,
                'piece_id' => 743,
                'complete' => null
            ],
            [
                'id' => 223,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 102,
                'piece_id' => 643,
                'complete' => null
            ],
            [
                'id' => 224,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 103,
                'piece_id' => 644,
                'complete' => null
            ],
            [
                'id' => 225,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 103,
                'piece_id' => 744,
                'complete' => null
            ],
            [
                'id' => 226,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 104,
                'piece_id' => 748,
                'complete' => null
            ],
            [
                'id' => 227,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 104,
                'piece_id' => 648,
                'complete' => null
            ],
            [
                'id' => 228,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 105,
                'piece_id' => 652,
                'complete' => null
            ],
            [
                'id' => 229,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 106,
                'piece_id' => 752,
                'complete' => null
            ],
            [
                'id' => 230,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 107,
                'piece_id' => 730,
                'complete' => null
            ],
            [
                'id' => 231,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 108,
                'piece_id' => 918,
                'complete' => null
            ],
            [
                'id' => 232,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 109,
                'piece_id' => 919,
                'complete' => null
            ],
            [
                'id' => 233,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 109,
                'piece_id' => 920,
                'complete' => null
            ],
            [
                'id' => 234,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 110,
                'piece_id' => 921,
                'complete' => null
            ],
            [
                'id' => 235,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 111,
                'piece_id' => 375,
                'complete' => null
            ],
            [
                'id' => 236,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 112,
                'piece_id' => 922,
                'complete' => null
            ],
            [
                'id' => 237,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 113,
                'piece_id' => 660,
                'complete' => null
            ],
            [
                'id' => 238,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 113,
                'piece_id' => 760,
                'complete' => null
            ],
            [
                'id' => 239,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 114,
                'piece_id' => 923,
                'complete' => null
            ],
            [
                'id' => 240,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 115,
                'piece_id' => 924,
                'complete' => null
            ],
            [
                'id' => 241,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 115,
                'piece_id' => 925,
                'complete' => null
            ],
            [
                'id' => 242,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 115,
                'piece_id' => 926,
                'complete' => null
            ],
            [
                'id' => 243,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 115,
                'piece_id' => 927,
                'complete' => null
            ],
            [
                'id' => 244,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 115,
                'piece_id' => 928,
                'complete' => null
            ],
            [
                'id' => 245,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 115,
                'piece_id' => 929,
                'complete' => null
            ],
            [
                'id' => 246,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 116,
                'piece_id' => 930,
                'complete' => null
            ],
            [
                'id' => 247,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 116,
                'piece_id' => 906,
                'complete' => null
            ],
            [
                'id' => 248,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 116,
                'piece_id' => 931,
                'complete' => null
            ],
            [
                'id' => 249,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 116,
                'piece_id' => 932,
                'complete' => null
            ],
            [
                'id' => 250,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 116,
                'piece_id' => 933,
                'complete' => null
            ],
            [
                'id' => 251,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 116,
                'piece_id' => 934,
                'complete' => null
            ],
            [
                'id' => 252,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 117,
                'piece_id' => 85,
                'complete' => null
            ],
            [
                'id' => 253,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 117,
                'piece_id' => 385,
                'complete' => null
            ],
            [
                'id' => 254,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 117,
                'piece_id' => 135,
                'complete' => null
            ],
            [
                'id' => 255,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 117,
                'piece_id' => 285,
                'complete' => null
            ],
            [
                'id' => 256,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 117,
                'piece_id' => 672,
                'complete' => null
            ],
            [
                'id' => 257,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 117,
                'piece_id' => 772,
                'complete' => null
            ],
            [
                'id' => 258,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 118,
                'piece_id' => 358,
                'complete' => null
            ],
            [
                'id' => 259,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 118,
                'piece_id' => 258,
                'complete' => null
            ],
            [
                'id' => 260,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 119,
                'piece_id' => 645,
                'complete' => null
            ],
            [
                'id' => 261,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 119,
                'piece_id' => 745,
                'complete' => null
            ],
            [
                'id' => 262,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 120,
                'piece_id' => 646,
                'complete' => null
            ],
            [
                'id' => 263,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 120,
                'piece_id' => 109,
                'complete' => null
            ],
            [
                'id' => 264,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 120,
                'piece_id' => 359,
                'complete' => null
            ],
            [
                'id' => 265,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 120,
                'piece_id' => 259,
                'complete' => null
            ],
            [
                'id' => 266,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 120,
                'piece_id' => 746,
                'complete' => null
            ],
            [
                'id' => 267,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 121,
                'piece_id' => 371,
                'complete' => null
            ],
            [
                'id' => 268,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 121,
                'piece_id' => 271,
                'complete' => null
            ],
            [
                'id' => 269,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 121,
                'piece_id' => 658,
                'complete' => null
            ],
            [
                'id' => 270,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 121,
                'piece_id' => 758,
                'complete' => null
            ],
            [
                'id' => 271,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 122,
                'piece_id' => 372,
                'complete' => null
            ],
            [
                'id' => 272,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 122,
                'piece_id' => 272,
                'complete' => null
            ],
            [
                'id' => 273,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 122,
                'piece_id' => 659,
                'complete' => null
            ],
            [
                'id' => 274,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 122,
                'piece_id' => 759,
                'complete' => null
            ],
            [
                'id' => 275,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 123,
                'piece_id' => 639,
                'complete' => null
            ],
            [
                'id' => 276,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 123,
                'piece_id' => 739,
                'complete' => null
            ],
            [
                'id' => 277,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 124,
                'piece_id' => 631,
                'complete' => null
            ],
            [
                'id' => 278,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 124,
                'piece_id' => 633,
                'complete' => null
            ],
            [
                'id' => 279,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 124,
                'piece_id' => 634,
                'complete' => null
            ],
            [
                'id' => 280,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 124,
                'piece_id' => 637,
                'complete' => null
            ],
            [
                'id' => 281,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 124,
                'piece_id' => 641,
                'complete' => null
            ],
            [
                'id' => 282,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 124,
                'piece_id' => 647,
                'complete' => null
            ],
            [
                'id' => 283,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 124,
                'piece_id' => 747,
                'complete' => null
            ],
            [
                'id' => 284,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 124,
                'piece_id' => 741,
                'complete' => null
            ],
            [
                'id' => 285,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 124,
                'piece_id' => 737,
                'complete' => null
            ],
            [
                'id' => 286,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 124,
                'piece_id' => 734,
                'complete' => null
            ],
            [
                'id' => 287,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 124,
                'piece_id' => 733,
                'complete' => null
            ],
            [
                'id' => 288,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 124,
                'piece_id' => 731,
                'complete' => null
            ],
            [
                'id' => 289,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 125,
                'piece_id' => 80,
                'complete' => null
            ],
            [
                'id' => 290,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 125,
                'piece_id' => 60,
                'complete' => null
            ],
            [
                'id' => 291,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 125,
                'piece_id' => 246,
                'complete' => null
            ],
            [
                'id' => 292,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 125,
                'piece_id' => 346,
                'complete' => null
            ],
            [
                'id' => 293,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 125,
                'piece_id' => 633,
                'complete' => null
            ],
            [
                'id' => 294,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 125,
                'piece_id' => 733,
                'complete' => null
            ],
            [
                'id' => 295,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 126,
                'piece_id' => 935,
                'complete' => null
            ],
            [
                'id' => 296,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 126,
                'piece_id' => 936,
                'complete' => null
            ],
            [
                'id' => 297,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 126,
                'piece_id' => 937,
                'complete' => null
            ],
            [
                'id' => 298,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 126,
                'piece_id' => 938,
                'complete' => null
            ],
            [
                'id' => 299,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 126,
                'piece_id' => 939,
                'complete' => null
            ],
            [
                'id' => 300,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 126,
                'piece_id' => 940,
                'complete' => null
            ],
            [
                'id' => 319,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 69,
                'complete' => null
            ],
            [
                'id' => 320,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 951,
                'complete' => null
            ],
            [
                'id' => 321,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 949,
                'complete' => null
            ],
            [
                'id' => 322,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 240,
                'complete' => null
            ],
            [
                'id' => 323,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 944,
                'complete' => null
            ],
            [
                'id' => 324,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 943,
                'complete' => null
            ],
            [
                'id' => 325,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 850,
                'complete' => null
            ],
            [
                'id' => 326,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 340,
                'complete' => null
            ],
            [
                'id' => 327,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 945,
                'complete' => null
            ],
            [
                'id' => 328,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 851,
                'complete' => null
            ],
            [
                'id' => 329,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 439,
                'complete' => null
            ],
            [
                'id' => 330,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 952,
                'complete' => null
            ],
            [
                'id' => 331,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 950,
                'complete' => null
            ],
            [
                'id' => 332,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 726,
                'complete' => null
            ],
            [
                'id' => 333,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 953,
                'complete' => null
            ],
            [
                'id' => 334,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 954,
                'complete' => null
            ],
            [
                'id' => 335,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 826,
                'complete' => null
            ],
            [
                'id' => 336,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 948,
                'complete' => null
            ],
            [
                'id' => 337,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 129,
                'piece_id' => 853,
                'complete' => null
            ],
            [
                'id' => 338,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 130,
                'piece_id' => 955,
                'complete' => null
            ],
            [
                'id' => 339,
                'created' => null,
                'modified' => null,
                'user_id' => null,
                'disposition_id' => 131,
                'piece_id' => 961,
                'complete' => null
            ],
        ];
        parent::init();
    }
}

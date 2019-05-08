<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ArtworksFixture
 *
 */
class ArtworksFixture extends TestFixture
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
        'image_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'title' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'description' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null],
        'edition_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
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
                'id' => 4,
                'created' => '2016-03-08 02:16:34',
                'modified' => '2016-03-15 03:40:37',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => 5,
                'title' => 'Jabberwocky',
                'description' => '',
                'edition_count' => 2
            ],
            [
                'id' => 5,
                'created' => '2016-03-08 02:17:13',
                'modified' => '2016-08-19 20:59:30',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => 6,
                'title' => '"Global Warming" Survival Kit',
                'description' => 'A Patriots first line of defense against an imaginary threat. But it\'s best to be prepared.',
                'edition_count' => 2
            ],
            [
                'id' => 6,
                'created' => '2016-03-08 02:17:57',
                'modified' => '2016-03-08 02:17:57',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => 7,
                'title' => 'Bill Owens: Jamaica',
                'description' => 'Work from Bill\'s 1968 Peace Corps trip to Jamaica',
                'edition_count' => 1
            ],
            [
                'id' => 7,
                'created' => '2016-03-09 21:24:23',
                'modified' => '2016-10-23 03:33:51',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => 9,
                'title' => 'Elementary Geography',
                'description' => 'Book one in the One Poem series.',
                'edition_count' => 3
            ],
            [
                'id' => 8,
                'created' => '2016-03-09 22:27:42',
                'modified' => '2016-10-05 19:55:28',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => 10,
                'title' => 'Crystal Day',
                'description' => 'Book two in the One Poem series',
                'edition_count' => 3
            ],
            [
                'id' => 9,
                'created' => '2016-03-10 19:00:23',
                'modified' => '2016-10-05 20:17:16',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => 25,
                'title' => 'Forecast',
                'description' => 'Book 3 in the One Poem Series',
                'edition_count' => 3
            ],
            [
                'id' => 10,
                'created' => '2016-03-10 19:01:40',
                'modified' => '2016-10-05 20:05:17',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => 23,
                'title' => 'Wild Geese',
                'description' => 'Book 4 in the One Poem Series',
                'edition_count' => 3
            ],
            [
                'id' => 11,
                'created' => '2016-06-02 15:19:19',
                'modified' => '2016-06-02 15:19:19',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => 11,
                'title' => 'Afternoons with Ruth',
                'description' => 'Book two of the Conversation Series',
                'edition_count' => 1
            ],
            [
                'id' => 13,
                'created' => '2016-08-28 01:02:43',
                'modified' => '2016-09-22 15:34:10',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => 13,
                'title' => 'The Day I Met George',
                'description' => '',
                'edition_count' => 1
            ],
            [
                'id' => 14,
                'created' => '2016-09-29 03:55:02',
                'modified' => '2016-09-29 03:55:02',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => null,
                'title' => 'A Critique of A Critique of Pure Reason',
                'description' => 'Counts of all the words in Emmanuel Kant\'s A Critique of Pure Reason',
                'edition_count' => 1
            ],
            [
                'id' => 16,
                'created' => '2016-10-05 16:29:48',
                'modified' => '2016-10-05 20:19:44',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => 19,
                'title' => 'Here',
                'description' => 'Book 5 in the One Poem Series',
                'edition_count' => 3
            ],
            [
                'id' => 17,
                'created' => '2016-10-05 16:38:48',
                'modified' => '2016-10-05 20:20:55',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => 21,
                'title' => 'End',
                'description' => 'Book 6 in the One Poem Series',
                'edition_count' => 3
            ],
            [
                'id' => 18,
                'created' => '2016-10-06 14:43:31',
                'modified' => '2016-10-06 14:43:31',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => null,
                'title' => 'Walking With Kandinsky',
                'description' => 'Book 1 of the Conversation Series',
                'edition_count' => 1
            ],
            [
                'id' => 19,
                'created' => '2018-04-06 03:04:48',
                'modified' => '2018-05-14 04:59:12',
                'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2',
                'image_id' => null,
                'title' => 'Sample Artwork',
                'description' => 'Limited edition that has multiple formats',
                'edition_count' => 2
            ],
        ];
        parent::init();
    }
}

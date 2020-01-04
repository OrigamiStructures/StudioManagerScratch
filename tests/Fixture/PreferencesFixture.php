<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PreferencesFixture
 */
class PreferencesFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'prefs' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'A serialized array of user preferences', 'precision' => null],
        'user_id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => '', 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'MyISAM',
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
                'created' => '2019-12-24 21:11:54',
                'modified' => '2019-12-24 21:11:54',
                'prefs' => '{"paginate":{"sort":{"people":"first_name"}}}',
                'user_id' => 'AA074ebc-758b-4729-91f3-bcd65e51ace4'
            ],
            [
                'id' => 2,
                'created' => '2019-12-24 21:11:54',
                'modified' => '2019-12-24 21:11:54',
                'prefs' => '{"paginate":{"sort":{"people":"first_name", "invalid":"pref"}}}',
                'user_id' => 'BB074ebc-758b-4729-91f3-bcd65e51ace4'
            ],
        ];
        parent::init();
    }
}

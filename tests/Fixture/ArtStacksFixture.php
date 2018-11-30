<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ArtStacksFixture
 *
 */
class ArtStacksFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'artwork' => ['type' => 'layer', 'length' => null, 'precision' => null, 'null' => null, 'default' => null, 'comment' => null],
        'editions' => ['type' => 'layer', 'length' => null, 'precision' => null, 'null' => null, 'default' => null, 'comment' => null],
        'formats' => ['type' => 'layer', 'length' => null, 'precision' => null, 'null' => null, 'default' => null, 'comment' => null],
        'pieces' => ['type' => 'layer', 'length' => null, 'precision' => null, 'null' => null, 'default' => null, 'comment' => null],
        'dispositionsPieces' => ['type' => 'layer', 'length' => null, 'precision' => null, 'null' => null, 'default' => null, 'comment' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_general_ci'
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
                'artwork' => '',
                'editions' => '',
                'formats' => '',
                'pieces' => '',
                'dispositionsPieces' => ''
            ],
        ];
        parent::init();
    }
}

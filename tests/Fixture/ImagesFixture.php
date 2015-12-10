<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ImagesFixture
 *
 */
class ImagesFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'image' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => '', 'comment' => 'The image file name', 'precision' => null, 'fixed' => null],
        'image_dir' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'mimetype' => ['type' => 'string', 'length' => 40, 'null' => true, 'default' => null, 'comment' => 'From EXIF data', 'precision' => null, 'fixed' => null],
        'filesize' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'From EXIF data', 'precision' => null, 'autoIncrement' => null],
        'width' => ['type' => 'integer', 'length' => 9, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'From EXIF data', 'precision' => null, 'autoIncrement' => null],
        'height' => ['type' => 'integer', 'length' => 9, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'From EXIF data', 'precision' => null, 'autoIncrement' => null],
        'title' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => '', 'comment' => 'HTML image title attribute', 'precision' => null, 'fixed' => null],
        'date' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'From EXIF data', 'precision' => null, 'autoIncrement' => null],
        'alt' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'comment' => 'HTML image alt attribute', 'precision' => null, 'fixed' => null],
        'upload' => ['type' => 'integer', 'length' => 16, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'Upload batch number', 'precision' => null, 'autoIncrement' => null],
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
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'modified' => '2015-12-10 21:29:32',
            'created' => '2015-12-10 21:29:32',
            'user_id' => 1,
            'image' => 'Lorem ipsum dolor sit amet',
            'image_dir' => 'Lorem ipsum dolor sit amet',
            'mimetype' => 'Lorem ipsum dolor sit amet',
            'filesize' => '',
            'width' => 1,
            'height' => 1,
            'title' => 'Lorem ipsum dolor sit amet',
            'date' => '',
            'alt' => 'Lorem ipsum dolor sit amet',
            'upload' => 1
        ],
    ];
}

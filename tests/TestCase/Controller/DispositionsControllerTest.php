<?php
namespace App\Test\TestCase\Controller;

use App\Controller\DispositionsController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\DispositionsController Test Case
 */
class DispositionsControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.dispositions',
        'app.users',
        'app.members',
        'app.images',
        'app.artworks',
        'app.editions',
        'app.series',
        'app.formats',
        'app.subscriptions',
        'app.pieces',
//        'app.dispositions_pieces',
        'app.locations',
        'app.addresses',
        'app.contacts',
//        'app.groups',
        'app.groups_members',
//        'app.proxy_members',
//        'app.proxy_groups'
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

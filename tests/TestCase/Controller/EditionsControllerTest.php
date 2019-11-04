<?php
namespace App\Test\TestCase\Controller;

use App\Controller\EditionsController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\EditionsController Test Case
 */
class EditionsControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.editions',
        'app.users',
        'app.members',
        'app.images',
        'app.artworks',
        'app.formats',
        'app.subscriptions',
        'app.pieces',
        'app.dispositions',
        'app.locations',
//        'app.groups',
        'app.groups_members',
        'app.series'
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

<?php
namespace App\Test\TestCase\Controller;

use App\Controller\MembersController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\MembersController Test Case
 */
class MembersControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.members',
        'app.users',
        'app.artworks',
        'app.images',
        'app.formats',
        'app.editions',
        'app.series',
        'app.pieces',
        'app.dispositions',
        'app.locations',
        'app.subscriptions',
//        'app.groups',
        'app.groups_members'
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

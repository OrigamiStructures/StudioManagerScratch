<?php
namespace App\Test\TestCase\Controller;

use App\Controller\AddressesController;
use Cake\TestSuite\IntegrationTestCase;
use App\Test\Fixture\AddressesFixture;

/**
 * App\Controller\AddressesController Test Case
 */
class AddressesControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.addresses',
        'app.users',
        'app.members',
        'app.images',
        'app.dispositions',
        'app.locations',
        'app.pieces',
//        'app.groups',
        'app.groups_members',
        'app.artworks',
        'app.editions',
        'app.series',
        'app.formats',
        'app.subscriptions'
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
//        $addresses = new AddressesFixture();
        $addresses = $this->getTableLocator()->get('Addresses');
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

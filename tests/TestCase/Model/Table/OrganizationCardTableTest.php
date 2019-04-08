<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OrganizationCardTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OrganizationCardTable Test Case
 */
class OrganizationCardTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\OrganizationCardTable
     */
    public $OrganizationCard;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('OrganizationCard') ? [] : ['className' => OrganizationCardTable::class];
        $this->OrganizationCard = TableRegistry::getTableLocator()->get('OrganizationCard', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->OrganizationCard);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

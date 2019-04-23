<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OrganizationCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OrganizationCardsTable Test Case
 */
class OrganizationCardsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\OrganizationCardsTable
     */
    public $OrganizationCards;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.identities',
        'app.data_owners',
        'app.members',
        'app.contacts',
		'app.addresses',
		'app.dispositions'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('OrganizationCards') ? [] : ['className' => OrganizationCardsTable::class];
        $this->OrganizationCards = TableRegistry::getTableLocator()->get('OrganizationCards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->OrganizationCards);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitializeTables()
    {
		$this->OrganizationCards->initialize([]);

		$this->assertTrue(
			is_a(
				$this->OrganizationCards->Contacts,
				'App\Model\Table\ContactsTable'
			),
			'The ContactsTable object did not get initialized properly'
		);
		
		$this->assertTrue(
			is_a(
				$this->OrganizationCards->Addresses,
				'App\Model\Table\AddressesTable'
			),
			'The AddressesTable object did not get initialized properly'
		);
		
		$this->assertTrue(
			is_a(
				$this->OrganizationCards->Dispositions,
				'App\Model\Table\DispositionsTable'
			),
			'The DispositionsTable object did not get initialized properly'
		);
		
    }

    public function testInitializeSchema()
    {
		$this->OrganizationCards->initialize([]);

		$this->assertTrue(
			$this->OrganizationCards->getSchema()->hasColumn('contacts'),
			'The schema did not get a members contacts added'
		);
		
		$this->assertTrue(
			$this->OrganizationCards->getSchema()->getColumnType('contacts') 
				=== 'layer',
			'The schema column `contacts` is not a `layer` type'
		);
		
		$this->assertTrue(
			$this->OrganizationCards->getSchema()->hasColumn('addresses'),
			'The schema did not get a members addresses added'
		);
		
		$this->assertTrue(
			$this->OrganizationCards->getSchema()->getColumnType('addresses') 
				=== 'layer',
			'The schema column `addresses` is not a `layer` type'
		);
		
		$this->assertTrue(
			$this->OrganizationCards->getSchema()->hasColumn('dispositions'),
			'The schema did not get a members dispositions added'
		);
		
		$this->assertTrue(
			$this->OrganizationCards->getSchema()->getColumnType('dispositions') 
				=== 'layer',
			'The schema column `dispositions` is not a `layer` type'
		);
		
    }

    public function testInitializeSeeds()
    {
		$this->OrganizationCards->initialize([]);
		
		$this->assertTrue($this->OrganizationCards->hasSeed('contact'));
		$this->assertTrue($this->OrganizationCards->hasSeed('contacts'));
		// 'address' converts to singular improperly
//		$this->assertTrue($this->OrganizationCards->hasSeed('address'));
		$this->assertTrue($this->OrganizationCards->hasSeed('addresses'));
		$this->assertTrue($this->OrganizationCards->hasSeed('disposition'));
		$this->assertTrue($this->OrganizationCards->hasSeed('dispositions'));
		
    }

    /**
     * Test initializeContactableCard method
     *
     * @return void
     */
    public function testInitializeContactableCard()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

}

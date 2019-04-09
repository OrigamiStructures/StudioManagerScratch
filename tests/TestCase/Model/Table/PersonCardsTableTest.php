<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PersonCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PersonCardsTable Test Case
 */
class PersonCardsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PersonCardsTable
     */
    public $PersonCardsTable;

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
        $config = TableRegistry::getTableLocator()->exists('PersonCards') ? [] : ['className' => PersonCardsTable::class];
        $this->PersonCardsTable = TableRegistry::getTableLocator()->get('PersonCards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PersonCardsTable);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitializeTables()
    {
		$this->PersonCardsTable->initialize([]);

		$this->assertTrue(
			is_a(
				$this->PersonCardsTable->Contacts,
				'App\Model\Table\ContactsTable'
			),
			'The ContactsTable object did not get initialized properly'
		);
		
		$this->assertTrue(
			is_a(
				$this->PersonCardsTable->Addresses,
				'App\Model\Table\AddressesTable'
			),
			'The AddressesTable object did not get initialized properly'
		);
		
		$this->assertTrue(
			is_a(
				$this->PersonCardsTable->Dispositions,
				'App\Model\Table\DispositionsTable'
			),
			'The DispositionsTable object did not get initialized properly'
		);
		
    }

    public function testInitializeSchema()
    {
		$this->PersonCardsTable->initialize([]);

		$this->assertTrue(
			$this->PersonCardsTable->getSchema()->hasColumn('contacts'),
			'The schema did not get a members contacts added'
		);
		
		$this->assertTrue(
			$this->PersonCardsTable->getSchema()->getColumnType('contacts') 
				=== 'layer',
			'The schema column `contacts` is not a `layer` type'
		);
		
		$this->assertTrue(
			$this->PersonCardsTable->getSchema()->hasColumn('addresses'),
			'The schema did not get a members addresses added'
		);
		
		$this->assertTrue(
			$this->PersonCardsTable->getSchema()->getColumnType('addresses') 
				=== 'layer',
			'The schema column `addresses` is not a `layer` type'
		);
		
		$this->assertTrue(
			$this->PersonCardsTable->getSchema()->hasColumn('dispositions'),
			'The schema did not get a members dispositions added'
		);
		
		$this->assertTrue(
			$this->PersonCardsTable->getSchema()->getColumnType('dispositions') 
				=== 'layer',
			'The schema column `dispositions` is not a `layer` type'
		);
		
    }

    public function testInitializeSeeds()
    {
		$this->PersonCardsTable->initialize([]);
		
		$this->assertTrue($this->PersonCardsTable->hasSeed('contact'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('contacts'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('address'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('addresses'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('disposition'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('dispositions'));
		
    }


}

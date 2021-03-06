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

	public $ContactsProduct;

	public $AddressesProduct;

	public $DispositionsProduct;

	public $ImageProduct;

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
		'app.images',
		'app.addresses',
		'app.dispositions',
		'app.users',
		'app.groups_members',
        'app.manifests',
        'app.shares'

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
		$this->ContactsProduct = $this->PersonCardsTable->find(
				'stacksFor',
				['seed' => 'contacts', 'ids' => [1,5]]
			);
//		foreach($this->ContactsProduct->load() as $Stack) {
////			debug($Stack->dispositions);
//		}
		//members 1 dondrake, 2 gaildrake
		//
		$this->AddressesProduct = $this->PersonCardsTable->find(
				'stacksFor',
				['seed' => 'addresses', 'ids' => [76,2]]
			);
		//members 1 dondrake, 2 gaildrake
		//
		$this->DispositionsProduct = $this->PersonCardsTable->find(
				'stacksFor',
				['seed' => 'dispositions', 'ids' => [129,131]]
			);
		$this->ImageProduct = $this->PersonCardsTable->find(
				'stacksFor',
				['seed' => 'image', 'ids' => [9,10]]
			);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
		\Cake\Cache\Cache::clear(FALSE, $this->PersonCardsTable->cacheName());
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

		$this->assertTrue(
			is_a(
				$this->PersonCardsTable->Images,
				'App\Model\Table\ImagesTable'
			),
			'The ImagesTable object did not get initialized properly'
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

		$this->assertTrue(
			$this->PersonCardsTable->getSchema()->hasColumn('image'),
			'The schema did not get a members image added'
		);

		$this->assertTrue(
			$this->PersonCardsTable->getSchema()->getColumnType('image')
				=== 'layer',
			'The schema column `image` is not a `layer` type'
		);

    }

    public function testInitializeSeeds()
    {
		$this->PersonCardsTable->initialize([]);

		$this->assertTrue($this->PersonCardsTable->hasSeed('identity'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('identities'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('data_owner'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('data_owners'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('membership'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('memberships'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('contact'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('contacts'));
		// suppress bad singularization
//		$this->assertTrue($this->PersonCardsTable->hasSeed('address'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('addresses'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('disposition'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('dispositions'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('image'));
		$this->assertTrue($this->PersonCardsTable->hasSeed('images'));

    }

	public function testContactsProduct() {
		$this->assertTrue($this->ContactsProduct->count() === 2,
				'ContactProduct does not contain 2 entities.');
		$this->assertArraySubset([1,2],$this->ContactsProduct->IDs(),
				'ContactProduct does not contain the 2 specific expected entities.');
		$this->assertTrue(
				count($this->ContactsProduct->getLayer('contacts')->toArray())
				=== 5,
				'The combined count of contacts was not 5 (4 + 1).'
			);
		$this->assertTrue(
				count($this->ContactsProduct->getLayer('addresses')->toArray())
				=== 3,
				'The combined count of addresses was not 3 (2 + 1).'
			);
		$this->assertTrue(
				count($this->ContactsProduct->getLayer('image')->toArray())
				=== 0,
				'The combined count of images was not 0 (0 + 0).'
			);
		$this->assertTrue(
				count($this->ContactsProduct->getLayer('dispositions')->toArray())
				=== 4,
				'The combined count of dispositions was not 4 (3 + 1).'
			);
	}

	public function testAddressesProduct() {
		$this->assertTrue($this->AddressesProduct->count() === 2,
				'AddressesProduct does not contain 2 entities.');
		$this->assertArraySubset([1,2],$this->AddressesProduct->IDs(),
				'AddressesProduct does not contain the 2 specific expected entities.');
		$this->assertTrue(
				count($this->AddressesProduct->getLayer('contacts')->toArray())
				=== 5,
				'The combined count of contacts was not 5 (4 + 1).'
			);
		$this->assertTrue(
				count($this->AddressesProduct->getLayer('addresses')->toArray())
				=== 3,
				'The combined count of addresses was not 3 (2 + 1).'
			);
		$this->assertTrue(
				count($this->AddressesProduct->getLayer('image')->toArray())
				=== 0,
				'The combined count of images was not 0 (0 + 0).'
			);
		$this->assertTrue(
				count($this->AddressesProduct->getLayer('dispositions')->toArray())
				=== 4,
				'The combined count of dispositions was not 4 (3 + 1).'
			);
	}

	public function testImageProduct() {
		$this->assertTrue($this->ImageProduct->count() === 1,
				'ImageProduct does not contain 1 entities.');
		$this->assertArraySubset([1],$this->ImageProduct->IDs(),
				'ImageProduct does not contain the 2 specific expected entities.');
		$this->assertTrue(
				count($this->ImageProduct->getLayer('contacts')->toArray())
				=== 4,
				'The combined count of contacts was not 4 (4 + 0).'
			);
		$this->assertTrue(
				count($this->ImageProduct->getLayer('addresses')->toArray())
				=== 2,
				'The combined count of addresses was not 2 (2 + 0).'
			);
		$this->assertTrue(
				count($this->ImageProduct->getLayer('image')->toArray())
				=== 0,
				'The combined count of images was not 0 (0 + 0).'
			);
		$this->assertTrue(
				count($this->ImageProduct->getLayer('dispositions')->toArray())
				=== 3,
				'The combined count of dispositions was not 3 (3 + 0).'
			);
	}

	public function testReceiverProduct() {
		$this->assertTrue($this->DispositionsProduct->count() === 2,
				'DispositionsProduct does not contain 2 entities.');
		$this->assertArraySubset([1,2],$this->DispositionsProduct->IDs(),
				'DispositionsProduct does not contain the 2 specific expected entities.');
		$this->assertTrue(
				count($this->DispositionsProduct->getLayer('contacts')->toArray())
				=== 5,
				'The combined count of contacts was not 5 (4 + 1).'
			);
		$this->assertTrue(
				count($this->DispositionsProduct->getLayer('addresses')->toArray())
				=== 3,
				'The combined count of addresses was not 3 (2 + 1).'
			);
		$this->assertTrue(
				count($this->DispositionsProduct->getLayer('image')->toArray())
				=== 0,
				'The combined count of images was not 0 (0 + 0).'
			);
		$this->assertTrue(
				count($this->DispositionsProduct->getLayer('dispositions')->toArray())
				=== 4,
				'The combined count of dispositions was not 4 (3 + 1).'
			);
	}

}

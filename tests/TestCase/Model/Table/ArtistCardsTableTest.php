<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ArtistCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ArtistCardsTable Test Case
 */
class ArtistCardsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ArtistCardsTable
     */
    public $AritstCardsTable;
	
	public $ArtistsProduct;

	public $ManagersProduct;

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
		'app.dispositions',
		'app.users',
		'app.groups_members',
		'app.artists'

    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()
				->exists('ArtistCards') 
				? [] 
				: ['className' => ArtistCardsTable::class];
        $this->AritstCardsTable = TableRegistry::getTableLocator()
				->get('ArtistCards', $config);
		$this->ArtistsProduct = $this->AritstCardsTable->find(
				'stacksFor', 
				['seed' => 'artists', 'ids' => [1,5]]
			);
		//members 1 dondrake, 2 gaildrake
		//
		$this->ManagersProduct = $this->AritstCardsTable->find(
				'stacksFor', 
				['seed' => 'managers', 'ids' => [
					'708cfc57-1162-4c5b-9092-42c25da131a9',
					'f22f9b46-345f-4c6f-9637-060ceacb21b2']
				]
			);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
		\Cake\Cache\Cache::clear(FALSE, $this->AritstCardsTable->cacheName());
        unset($this->AritstCardsTable);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitializeTables()
    {
		$this->AritstCardsTable->initialize([]);

		$this->assertTrue(
			is_a(
				$this->AritstCardsTable->Artists,
				'App\Model\Table\ArtistsTable'
			),
			'The ArtistsTable object did not get initialized properly'
		);
		
    }

    public function testInitializeSchema()
    {
		$this->AritstCardsTable->initialize([]);

		$this->assertTrue(
			$this->AritstCardsTable->getSchema()->hasColumn('artists'),
			'The schema did not get an artists column added'
		);
		
		$this->assertTrue(
			$this->AritstCardsTable->getSchema()->getColumnType('artists') 
				=== 'layer',
			'The schema column `artists` is not a `layer` type'
		);
		
		$this->assertTrue(
			$this->AritstCardsTable->getSchema()->hasColumn('managers'),
			'The schema did not get a managers column added'
		);
		
		$this->assertTrue(
			$this->AritstCardsTable->getSchema()->getColumnType('managers') 
				=== 'layer',
			'The schema column `managers` is not a `layer` type'
		);
		
    }

    public function testInitializeSeeds()
    {
		$this->AritstCardsTable->initialize([]);
		
		$this->assertTrue($this->AritstCardsTable->hasSeed('identity'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('identities'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('data_owner'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('data_owners'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('membership'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('memberships'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('contact'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('contacts'));
		// suppress bad singularization
//		$this->assertTrue($this->PersonCardsTable->hasSeed('address'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('addresses'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('disposition'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('dispositions'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('image'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('images'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('artist'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('artists'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('manager'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('managers'));
		
    }

	public function testArtistsProduct() {
//		debug($this->ArtistsProduct);
		$this->assertTrue($this->ArtistsProduct->count() === 2, 
				'ArtistProduct does not contain 2 entities.');
		$this->assertArraySubset([1,75],$this->ArtistsProduct->IDs(),
				'ArtistProduct does not contain the 2 specific expected entities.');
		$this->assertTrue(
				count($this->ArtistsProduct->find()->setLayer('contacts')->load())
				=== 4,
				'The combined count of contacts was not 4 (4 + 0).'
			);
		$this->assertTrue(
				count($this->ArtistsProduct->find()->setLayer('addresses')->load())
				=== 2,
				'The combined count of addresses was not 2 (2 + 0).'
			);
		$this->assertTrue(
				count($this->ArtistsProduct->find()->setLayer('image')->load())
				=== 1,
				'The combined count of images was not 1 (1 + 0).'
			);
		$this->assertTrue(
				count($this->ArtistsProduct->find()->setLayer('dispositions')->load())
				=== 3,
				'The combined count of dispositions was not 3 (3 + 0).'
			);
		$this->assertTrue(
				count($this->ArtistsProduct->find()->setLayer('artists')->load())
				=== 3,
				'The combined count of artists was not 3 (1 + 2).'
			);
		$this->assertTrue(
				count($this->ArtistsProduct->find()->setLayer('managers')->load())
				=== 3,
				'The combined count of managers was not 3 (1 + 2).'
				. 'load() needs to be changed. String keys are overlapping '
				. 'and preventing a full count of linked records'
			);
	}
	
	public function testManagersProduct() {
		$this->assertTrue($this->ManagersProduct->count() === 3, 
				'ManagersProduct does not contain 2 entities.');
		$this->assertArraySubset([1,2,75],$this->ManagersProduct->IDs(),
				'ManagersProduct does not contain the 2 specific expected entities.');
		$this->assertTrue(
				count($this->ManagersProduct->find()->setLayer('contacts')->load())
				=== 5,
				'The combined count of contacts was not 5 (4 + 1 + 0).'
			);
		$this->assertTrue(
				count($this->ManagersProduct->find()->setLayer('addresses')->load())
				=== 3,
				'The combined count of addresses was not 3 (2 + 1 + 0).'
			);
		$this->assertTrue(
				count($this->ManagersProduct->find()->setLayer('image')->load())
				=== 2,
				'The combined count of images was not 2 (1 + 1 + 0).'
			);
		$this->assertTrue(
				count($this->ManagersProduct->find()->setLayer('dispositions')->load())
				=== 4,
				'The combined count of dispositions was not 4 (3 + 1 + 0).'
			);
		$this->assertTrue(
				count($this->ManagersProduct->find()->setLayer('artists')->load())
				=== 5,
				'The combined count of images was not 5.'
			);
		$this->assertTrue(
				count($this->ManagersProduct->find()->setLayer('managers')->load())
				=== 5,
				'The combined count of dispositions was not 4 (1 + 2 + 2).'
				. 'load() needs to be changed. String keys are overlapping '
				. 'and preventing a full count of linked records'
			);
	}

}

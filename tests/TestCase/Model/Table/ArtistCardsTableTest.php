<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Lib\StackSet;
use App\Model\Table\ArtistCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Cache\Cache;

/**
 * App\Model\Table\ArtistCardsTable Test Case
 *
 * @property StackSet $ManifestProduct
 */
class ArtistCardsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ArtistCardsTable
     */
    public $AritstCardsTable;

	public $ManifestsProduct;

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
		'app.images',
		'app.groups_members',
		'app.manifests',
		'app.artworks'
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
		$this->ManifestsProduct = $this->AritstCardsTable->find(
				'stacksFor',
				['seed' => 'manifests', 'ids' => [1,5]]
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
 		Cache::clear(FALSE, $this->AritstCardsTable->cacheName());
   }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ArtistCardsTable);

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
				$this->AritstCardsTable->Manifests,
				'App\Model\Table\ManifestsTable'
			),
			'The ManifestsTable object did not get initialized properly'
		);

    }

    public function testInitializeSchema()
    {
		$this->AritstCardsTable->initialize([]);

		$this->assertTrue(
			$this->AritstCardsTable->getSchema()->hasColumn('manifest'),
			'The schema did not get an manifest column added'
		);

		$this->assertTrue(
			$this->AritstCardsTable->getSchema()->getColumnType('manifest')
				=== 'layer',
			'The schema column `manifest` is not a `layer` type'
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
		$this->assertTrue($this->AritstCardsTable->hasSeed('manifest'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('manifests'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('manager'));
		$this->assertTrue($this->AritstCardsTable->hasSeed('managers'));

    }

	public function testManifestsProduct() {
//		debug($this->ManifestsProduct);
		$this->assertTrue($this->ManifestsProduct->count() === 2,
				'ArtistProduct does not contain 2 entities.');
		$this->assertArraySubset([1,75],$this->ManifestsProduct->IDs(),
				'ArtistProduct does not contain the 2 specific expected entities.');
		$this->assertTrue(
				count($this->ManifestsProduct->getLayer('contacts')->toArray())
				=== 4,
				'The combined count of contacts was not 4 (4 + 0).'
			);
		$this->assertTrue(
				count($this->ManifestsProduct->getLayer('addresses')->toArray())
				=== 2,
				'The combined count of addresses was not 2 (2 + 0).'
			);
		$this->assertTrue(
				count($this->ManifestsProduct->getLayer('image')->toArray())
				=== 0,
				'The combined count of images was not 0 (0 + 0).'
			);
		$this->assertTrue(
				count($this->ManifestsProduct->getLayer('dispositions')->toArray())
				=== 3,
				'The combined count of dispositions was not 3 (3 + 0).'
			);
		$this->assertTrue(
				count($this->ManifestsProduct->getLayer('manifest')->toArray())
				=== 4,
				'The combined count of manifest was not 4 (2 + 2).'
			);
		$this->assertTrue(
				count($this->ManifestsProduct->getLayer('managers')->toArray())
				=== 4,
				'The combined count of managers was not 4 (2 + 2).'
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
				count($this->ManagersProduct->getLayer('contacts')->toArray())
				=== 5,
				'The combined count of contacts was not 5 (4 + 1 + 0).'
			);
		$this->assertTrue(
				count($this->ManagersProduct->getLayer('addresses')->toArray())
				=== 3,
				'The combined count of addresses was not 3 (2 + 1 + 0).'
			);
		$this->assertTrue(
				count($this->ManagersProduct->getLayer('image')->toArray())
				=== 0,
				'The combined count of images was not 0 (0 + 0 + 0).'
			);
		$this->assertTrue(
				count($this->ManagersProduct->getLayer('dispositions')->toArray())
				=== 4,
				'The combined count of dispositions was not 4 (3 + 1 + 0).'
			);
		$this->assertTrue(
				count($this->ManagersProduct->getLayer('manifest')->toArray())
				=== 5,
				'The combined count of images was not 5.'
			);
		$this->assertTrue(
				count($this->ManagersProduct->getLayer('managers')->toArray())
				=== 5,
				'The combined count of managers was not 4 (1 + 2 + 2).'
				. 'load() needs to be changed. String keys are overlapping '
				. 'and preventing a full count of linked records'
			);
	}

}

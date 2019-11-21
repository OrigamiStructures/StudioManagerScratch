<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ArtistManifestsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Cache\Cache;

/**
 * App\Model\Table\ArtistManifestsTable Test Case
 */
class ArtistManifestsTableTest extends TestCase
{

	public $ArtistManifests;

    /**
     * Test subject
     *
     * @var \App\Model\Table\ArtistManifestsTable
     */
    public $ArtistManifestsTable;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
			'app.members',
			'app.users',
			'app.manifests',
			'app.permissions'
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
				->exists('ArtistManifests')
				? []
				: ['className' => ArtistManifestsTable::class];
        $this->ArtistManifestsTable = TableRegistry::getTableLocator()
				->get('ArtistManifests', $config);

		$this->ArtistManifests = $this->ArtistManifestsTable
				->find('stacksFor', ['seed' => 'identity', 'ids' => [1]]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
		Cache::clear(FALSE, $this->ArtistManifestsTable->cacheName());
        unset($this->ArtistManifestsTable);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {

        $this->assertTrue(
            is_a(
                $this->ArtistManifestsTable->Identities,
                'App\Model\Table\IdentitiesTable'),

            'Initialize() did not set up IdentitiesTable (alias for MembersTable).'
        );


        $this->assertTrue(
            is_a(
                $this->ArtistManifestsTable->DataOwners,
                'App\Model\Table\DataOwnersTable'),

            'Initialize() did not set up DataOwnersTable.'
        );


        $this->assertTrue(
            is_a(
                $this->ArtistManifestsTable->Manifests,
                'App\Model\Table\ManifestsTable'),

            'Initialize() did not set up ManifestsTable.'
        );


        $this->assertTrue(
            is_a(
                $this->ArtistManifestsTable->Permissions,
                'App\Model\Table\PermissionsTable'),

            'Initialize() did not set up PermissionsTable.'
        );

    }

    public function testArtistManifestsBasicStructure()
    {

        $this->assertTrue(
            is_a($this->ArtistManifests, 'App\Model\Lib\StackSet'),
            'The found cards did not come packaged in a StackSet.'
        );

        $manifest = $this->ArtistManifests->element(0);

        $this->assertInstanceOf('App\Model\Entity\ArtistManifest', $manifest,
            'The StackSet does not contain ArtistManifest instances.'
        );

        $this->assertInstanceOf('App\Model\Lib\Layer', $manifest->identity,
            'The cards identity property is not a Layer object');

        $this->assertInstanceOf(
				'App\Model\Entity\Identity',
				$manifest->identity->element(0),
            'The cards identity layer does not contain Identity entity objects');

        $this->assertInstanceOf('App\Model\Lib\Layer', $manifest->data_owner,
            'The cards data_owner property is not a Layer object');

        $this->assertInstanceOf(
				'App\Model\Entity\DataOwner',
				$manifest->data_owner->element(0),
            'The card\'s data_owner does not contain DataOwner entity instances.'
        );

        $this->assertInstanceOf('App\Model\Lib\Layer', $manifest->manifests,
            'The cards manifests property is not a Layer object');

        $this->assertInstanceOf(
				'App\Model\Entity\Manifest',
				$manifest->manifests->element(0),
            'The card\'s manifests does not contain Manifest instances.'
        );

        $this->assertInstanceOf('App\Model\Lib\Layer', $manifest->managers,
            'The cards managers property is not a Layer object');

        $this->assertInstanceOf(
				'App\Model\Entity\DataOwner',
				$manifest->managers->element(0),
            'The card\'s managers does not contain DataOwner instances.'
        );

        $this->assertInstanceOf('App\Model\Lib\Layer', $manifest->permissions,
            'The cards permissions property is not a Layer object');

        $this->assertInstanceOf(
				'App\Model\Entity\Permission',
				$manifest->permissions->element(0),
            'The card\'s permissions does not contain Permission instances.'
        );
    }


    public function testAritstManifestDataQuantity() {
        $manifest = $this->ArtistManifests->element(0);
        
        $this->assertCount(1, $manifest->identity->load(),
            'The manifest doesn\'t have a single Identity entity');
        
        $this->assertCount(1, $manifest->data_owner->load(),
            'The manifest doesn\'t have a single DataOwner entity');
        
        $this->assertCount(2, $manifest->manifests->load(),
            'The manifest doesn\'t have a two Manifest entities');
        
        $this->assertCount(2, $manifest->managers->load(),
            'The manifest doesn\'t have a two manager entities');
        
        $this->assertCount(3, $manifest->permissions->load(),
            'The manifest doesn\'t have a three Permissions entities');
	}

    public function testAritstManifestDataQuality() {
        $manifest = $this->ArtistManifests->element(0);

        $this->assertEquals('Don Drake', $manifest->rootElement()->name(),
            'Not the person name expected');

        $this->assertEquals(
            'f22f9b46-345f-4c6f-9637-060ceacb21b2',
            $manifest->data_owner->element(0)->id(),
            'Not the owner expected');

        $this->assertEquals(
            [1,3],
            $manifest->manifests->IDs(),
            'Not the manifests expected');

        $this->assertEquals([
			'708cfc57-1162-4c5b-9092-42c25da131a9',
			'f22f9b46-345f-4c6f-9637-060ceacb21b2'
		], $manifest->managers->IDs(),
            'Not the managers expected');

        $this->assertEquals([1,2,3], $manifest->permissions->IDs(),
            'Not the permissions expected');
	}

}

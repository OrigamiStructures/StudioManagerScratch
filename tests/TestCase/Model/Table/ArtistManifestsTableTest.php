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
		debug($this->ArtistManifests);
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
		debug($this->ArtistManifests);
        $this->markTestIncomplete('Not implemented yet.');
    }
}

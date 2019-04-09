<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CategoryCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CategoryCardsTable Test Case
 */
class CategoryCardsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CategoryCardsTable
     */
    public $CategoryCardsTable;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.identities',
        'app.data_owners',
        'app.members'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CategoryCards') ? [] : ['className' => CategoryCardsTable::class];
        $this->CategoryCardsTable = TableRegistry::getTableLocator()->get('CategoryCards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CategoryCardsTable);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
		$this->CategoryCardsTable->initialize([]);

		$this->assertTrue(
			is_a(
				$this->CategoryCardsTable->Members,
				'App\Model\Table\MembersTable'
			),
			'The MembersTable object did not get initialized properly'
		);
		
		$this->assertTrue(
			$this->CategoryCardsTable->getSchema()->hasColumn('members'),
			'The schema did not get a members column added'
		);
		
		$this->assertTrue(
			$this->CategoryCardsTable->getSchema()->getColumnType('members') 
				=== 'layer',
			'The schema column `members` is not a `layer` type'
		);
		
		$this->assertTrue($this->CategoryCardsTable->hasSeed('members'));
		$this->assertTrue($this->CategoryCardsTable->hasSeed('member'));
		
    }
	
	public function testCartegoryCardsData() {
		
	}
}

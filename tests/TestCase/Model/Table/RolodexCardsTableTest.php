<?php
namespace App\Test\TestCase\Model\Table;

use App\Exception\UnknownTableException;
use App\Exception\MissingMarshallerException;
use App\Exception\MissingDistillerMethodException;
use App\Model\Table\RolodexCardsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RolodexCardsTable Test Case
 */
class RolodexCardsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\RolodexCardsTable
     */
    public $RolodexCards;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.members',
        'app.users',
        'app.groups_members',
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
        $config = TableRegistry::getTableLocator()
            ->exists('RolodexCards') ? [] : ['className' => RolodexCardsTable::class];
        $this->RolodexCards = TableRegistry::getTableLocator()
            ->get('RolodexCards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RolodexCards);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testModelStructure()
    {

        $this->assertTrue(
            is_a(
                $this->RolodexCards->Identities,
                'App\Model\Table\IdentitiesTable'),

            'Initialize() did not set up IdentitiesTable (alias for MembersTable).'
        );

        $this->assertTrue(
            is_a(
                $this->RolodexCards->associations()->get('Memberships'),
                'Cake\ORM\Association\BelongsToMany'),

            'Initialize() did not set up MembershipsTable (alias for '
            . 'MembersTable which creates the Memberships layer).'
        );

        $this->assertTrue(
            is_a(
                $this->RolodexCards->associations()->get('DataOwners'),
                'Cake\ORM\Association\BelongsTo'),

            'Initialize() did not set up DataOwnersTable (alias for UsersTable).'
        );

    }


	public function testLayer() {
		$stackTable = new TestStack(); //defined at bottom of this page
		$expected = [
			'artwork',
			'editions',
			'formats',
			'pieces',
			'dispositionsPieces'
		];
		$this->assertEquals($expected, $stackTable->layers());
	}
    /**
     * Test findRolodexCards method
     *
     * @return void
     */
    public function testFindRolodexCardsBasicStructure()
    {
        $targets = ['seed' => 'identity', 'ids' => [2,3]];
        $cards = $this->RolodexCards->find('stacksFor', $targets);
//        pr($cards);

        $this->assertTrue(
            is_a($cards, 'App\Model\Lib\StackSet'),
            'The found cards did not come packaged in a StackSet.'
        );

        $card = $cards->element(2, LAYERACC_ID);

        $this->assertInstanceOf('App\Model\Entity\RolodexCard', $card,
            'The StackSet does not contain RolodexCard instances.'
        );

        $this->assertInstanceOf('App\Model\Lib\Layer', $card->identity,
            'The cards identity property is not a Layer object');

        $this->assertInstanceOf('App\Model\Entity\Identity', $card->identity->element(0),
            'The cards identity layer does not contain Identity entity objects');

        $this->assertInstanceOf('App\Model\Lib\Layer', $card->data_owner,
            'The cards data_owner property is not a Layer object');

        $this->assertInstanceOf('App\Model\Entity\DataOwner', $card->data_owner->element(0),
            'The card\'s data_owner does not contain DataOwner entity instances.'
        );

        $this->assertInstanceOf('App\Model\Lib\Layer', $card->memberships,
            'The cards memberships property is not a Layer object');

        $this->assertInstanceOf('App\Model\Entity\Membership', $card->memberships->element(0),
            'The card\'s memberships does not contain Membership instances.'
        );
    }

    public function testRolodexCardDataQuantity() {
        $targets = ['seed' => 'identity', 'ids' => [2,3]];
        $cards = $this->RolodexCards->find('stacksFor', $targets);

        $person = $cards->element(2, LAYERACC_ID);
        $group = $cards->element(3, LAYERACC_ID);

        $this->assertCount(1, $person->identity->toArray(),
            'The person card doesn\'t have a single Identity entity');

        $this->assertCount(1, $group->identity->toArray(),
            'The group card doesn\'t have a single Identity entity');

        $this->assertCount(1, $person->data_owner->toArray(),
            'The person card doesn\'t have a single DataOwner entity');

        $this->assertCount(1, $group->data_owner->toArray(),
            'The group card doesn\'t have a single DataOwner entity');

        $this->assertCount(2, $person->memberships->toArray(),
            'The person card doesn\'t have a two Membership entities');

        $this->assertCount(0, $group->memberships->toArray(),
            'The group card has some Membership entities when it shouldn\'t');

    }

    public function testRolodexCardDataQuality() {
        $targets = ['seed' => 'identity', 'ids' => [2,3]];
        $cards = $this->RolodexCards->find('stacksFor', $targets);
//        pr($cards);
        //'008ab31c-124d-4e15-a4e1-45fccd7becac'

        $person = $cards->element(2, LAYERACC_ID);
        $group = $cards->element(3, LAYERACC_ID);

        $this->assertEquals('Gail Drake', $person->identity->element(0)->name(),
            'Not the person name expected');

        $this->assertEquals('Drake Family', $group->identity->element(0)->name(),
            'Not the group name expected');

        $this->assertEquals(
            'f22f9b46-345f-4c6f-9637-060ceacb21b2',
            $person->data_owner->element(0)->id(),
            'Not the owner expected');

        $this->assertEquals(
            'f22f9b46-345f-4c6f-9637-060ceacb21b2',
            $group->data_owner->element(0)->id(),
            'Not the owner expected');

        $this->assertEquals('Drake Family', $person->memberships->element(0)->name(),
            'Not the membership name expected');

        $this->assertEquals('Wonderland Group', $person->memberships->element(1)->name(),
            'Not the membership name expected');

    }

	public function testStackFromMembership() {
		$cards = $this->RolodexCards->find(
				'stacksFor',
				['seed' => 'membership', 'ids' => [4]]);
		$this->assertCount(2, $cards,
				'building from membership ids did not find the right '
				. 'number of stacks');
		$this->assertArraySubset(
			[1,2],
			$cards
				->getLayer('identity')
				->toValueList('id'),
				'building from membership ids did not pull the correct '
				. 'identity records to head the stacks');
	}

	public function testStackFromDataOwner() {
		$cards = $this->RolodexCards->find(
				'stacksFor',
				['seed' => 'data_owner', 'ids' => ['f22f9b46-345f-4c6f-9637-060ceacb21b2']]);
		$this->assertCount(9, $cards,
				'building from data_owner ids did not find the right '
				. 'number of stacks');
		$this->assertArraySubset(
			[1,2,3,4,5,6,7,8,9],
			$cards
                ->getLayer('identity')
				->toValueList('id'),
				'building from data_owner ids did not pull the correct '
				. 'identity records to head the stacks');
	}


}

class TestStack extends \App\Model\Table\StacksTable {

	protected $rootName = 'artwork';
	protected $stackSchema = 	[
            ['name' => 'artwork',				'specs' => ['type' => 'layer']],
            ['name' => 'editions',				'specs' => ['type' => 'layer']],
            ['name' => 'formats',				'specs' => ['type' => 'layer']],
            ['name' => 'stringy',				'specs' => ['type' => 'string']],
            ['name' => 'pieces',				'specs' => ['type' => 'layer']],
            ['name' => 'dispositionsPieces',	'specs' => ['type' => 'layer']],
            ['name' => 'other',					'specs' => ['type' => 'integer']],
        ];

}

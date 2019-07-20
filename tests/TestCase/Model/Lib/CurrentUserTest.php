<?php
namespace App\Test\TestCase\Model\Lib;

use App\Model\Lib\CurrentUser;
use Cake\TestSuite\TestCase;
use App\Model\Entity\PersonCard;
use App\Model\Table\PersonCardsTable;
use Cake\ORM\TableRegistry;
use App\Model\Table\RolodexCardsTable;
use App\Model\Behavior\IntegerQueryBehavior;
use Cake\ORM\Locator\TableLocator;
/**
 * App\Model\Lib\CurrentUser Test Case
 */
class CurrentUserTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Lib\CurrentUser
     */
    public $CurrentUser;
	
	public $PersonCard = null;
	
	public $PersonCardTable = null;
	
	public $TableLocator;

	public function setUp() {
		parent::setUp();
		
		$this->CurrentUser = new CurrentUser(	[
				'id' => 'testId',
				'management_token' => 'testToken',
				'username' => 'testName',
				'email' => 'testEmail',
				'first_name' => 'firstname',
				'last_name' => 'lastname',
				'active' => true,
				'is_superuser' => false,
				'role' => 'user',
				'artist_id' => 'testArtistId',
				'member_id' => 'testMemberId'
			]
		);
	}
	
	public function tearDown() {
		unset($this->CurrentUser);
		unset($this->PersonCardTable);
		unset($this->PersonCard);
		unset($this->TableLocator);
	}

    /**
     * Test managerId method
     *
     * @return void
     */
    public function testManagerId()
    {
        $this->assertTrue($this->CurrentUser->managerId() === 'testToken');
    }

    /**
     * Test supervisorId method
     *
     * @return void
     */
    public function testSupervisorId()
    {
        $this->assertTrue($this->CurrentUser->supervisorId() === 'testToken');
    }

    /**
     * Test userId method
     *
     * @return void
     */
    public function testUserId()
    {
        $this->assertTrue($this->CurrentUser->userId() === 'testId');
    }

    /**
     * Test name method
     *
     * @return void
     */
    public function testName()
    {
		$this->PersonCard = $this->createMock(PersonCard::class);
		$this->PersonCard->method('name')
				->will($this->returnValue('TestName'));
		$this->PersonCard->method('element')
				->will($this->returnValue($this->PersonCard));
		
		$this->PersonCardTable = $this->createMock(\Cake\ORM\Table::class);
		$this->PersonCardTable->method('find')
				->will($this->returnValue($this->PersonCard));
		
		$this->TableLocator = $this->createMock(TableLocator::class);
		$this->TableLocator->method('get')
				->will($this->returnValue($this->PersonCardTable));
		
		TableRegistry::setTableLocator($this->TableLocator);
		
        $this->assertTrue($this->CurrentUser->name() === 'TestName');
		
		// Gotta get a real locator back in the Registry
		TableRegistry::setTableLocator(new TableLocator());
    }

    /**
     * Test isSuperuser method
     *
     * @return void
     */
    public function testIsSuperuser()
    {
		$CurrentUser = new CurrentUser(	[
				'id' => 'testId',
				'is_superuser' => true,
			]
		);
        $this->assertTrue($CurrentUser->isSuperuser());
    }

    public function testIsNotSuperuser()
    {
		$CurrentUser = new CurrentUser(	[
				'id' => 'testId',
				'is_superuser' => false,
			]
		);
        $this->assertFalse($CurrentUser->isSuperuser());
    }

    /**
     * Test isActive method
     *
     * @return void
     */
    public function testIsActive()
    {
		$CurrentUser = new CurrentUser(	[
				'id' => 'testId',
				'active' => true,
			]
		);
        $this->assertTrue($CurrentUser->isActive());
    }
	
    public function testIsNotActive()
    {
		$CurrentUser = new CurrentUser(	[
				'id' => 'testId',
				'active' => false,
			]
		);
        $this->assertFalse($CurrentUser->isActive());
    }
}

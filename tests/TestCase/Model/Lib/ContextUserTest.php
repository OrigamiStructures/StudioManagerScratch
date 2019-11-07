<?php
namespace App\Test\TestCase\Model\Lib;

use App\Model\Lib\ContextUser;
use Cake\TestSuite\TestCase;
use Cake\Http\Session;

/**
 * App\Model\Lib\ContextUser Test Case
 */
class ContextUserTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Lib\ContextUser
     */
    public $ContextUser;

	public $CurrentUser;

	public $Session;

	public $options;

	public $AuthUser;

	public function setUp() {
		/**
		 * The first Session::read gets Auth.User
		 */
		$this->AuthUser = [
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
		];
		/**
		 * Mock Session rather than use the real thing.
		 * Configuration of the mock happens in the specific tests
		 * so different call results can be modeled
		 */
		$this->Session = $this->createMock(\Cake\Http\Session::class);
		/**
		 * Normally a CurrentUser object is built. But  we will mock it also.
		 * This object is part of the persisted ContextUser data.
		 */
		$this->CurrentUser = $this->createMock(\App\Model\Lib\CurrentUser::class);
		$this->CurrentUser
				->method('userId')
				->will(
						$this->onConsecutiveCalls('testId','testId','testId','testId','testId')
					);

		/**
		 * To inject these mocked objects I had to make instance( ) accept an
		 * argument. There is no validation of the passed args, so if this
		 * pathway comes into general use...
		 */
		$this->options = ['session' => $this->Session, 'currentUser' => $this->CurrentUser];
	}

	public function tearDown() {
//		unset($this->Session);
	}

    /**
     * Test instance method
     *
	 * @expectedException \Exception
     * @return void
     */
    public function testInstanceWithNoSessionUser()
    {
		$ContextUser = ContextUser::instance();
    }

	public function testInstanceWithNoPersistedVersion()
    {
		// What we expect to see in __debugInfo at the end
		$structure = [
			'user' => 'CurrentUser object:  testId',
			'actorId' => [
				'artist' => null,
				'manager' => null,
				'supervisor' => null
			],
			'actorCard' => [
				'artist' => null,
				'manager' => null,
				'supervisor' => null
			],
			'Session' => 'Session object',
			'PersonCardsTable' => 'Not set',
			'instance' => 'instance is populated'
		];

		// configure the Session mock for 'No Previously Stored Context'
		$this->Session->method('read')
             ->will($this->onConsecutiveCalls($this->AuthUser, NULL));

		$ContextUser = ContextUser::instance($this->options);

        $this->assertTrue(get_class($ContextUser) === 'App\Model\Lib\ContextUser',
				'::instance() without prior session data did not return a '
				. 'ContextUser object instance');

		$this->assertTrue($ContextUser === $ContextUser::instance(),
				'The context user does not store a reference to itself');

		$this->assertArraySubset($ContextUser->__debugInfo(), $structure);

		$ContextUser->tearDown();
	}

	public function testInstanceFromPersistedVersion() {
        $ContextUser = $this->starter();

        $this->assertTrue(get_class($ContextUser) === 'App\Model\Lib\ContextUser',
				'::instance() without prior session data did not return a '
				. 'ContextUser object instance');

		$this->assertTrue($ContextUser === $ContextUser::instance(),
				'The context user does not store a reference to itself');

		$this->assertTrue($ContextUser->getId('artist') === 'a-id',
				'an actorId value did not migrate from the session into '
				. 'the live object');

		$this->assertTrue(is_object($ContextUser->getCard('supervisor')),
				'an supervisor card did not migrate from the session into '
				. 'the live object');

		$ContextUser->tearDown();

	}
    /**
     * Test has method
     *
     * @return void
     */
    public function testHas()
    {
        $ContextUser = $this->starter();

		$this->assertTrue($ContextUser->has('artist'));
		$this->assertFalse($ContextUser->has('MANAGER'));

		$ContextUser->tearDown();
    }

    /**
     * Test set method
     *
     * @return void
     */
    public function testSet()
    {
        $ContextUser = $this->starter();

		$this->assertFalse($ContextUser->has('MANAGER'));
		$ContextUser->set('manager', 1);
		$this->assertTrue($ContextUser->has('MANAGER'));

		$ContextUser->tearDown();
    }

    /**
     * Test getId method
     *
     * @return void
     */
    public function testGetId()
    {
        $ContextUser = $this->starter();

		$this->assertTrue($ContextUser->getId('artist') === 'a-id');

		$ContextUser->tearDown();
    }

    /**
     * Test getCard method
     *
     * @return void
     */
    public function testGetCard()
    {
        $ContextUser = $this->starter();

		$this->assertTrue(is_object($ContextUser->getCard('supervisor')));

		$ContextUser->tearDown();
    }

    /**
     * Test clear method
     *
     * @return void
     */
    public function testClearWithNoArgs()
    {
        $ContextUser = $this->starter();

		$ContextUser->clear();

		$this->assertFalse($ContextUser->has('artist'));
		$this->assertFalse($ContextUser->has('supervisor'));

		$ContextUser->tearDown();
    }

	public function testClearWithArg() {
        $ContextUser = $this->starter();

		$this->assertTrue(is_object($ContextUser->getCard('supervisor')));
		$ContextUser->clear('SuperVisor');
		$this->assertTrue(is_null($ContextUser->getCard('supervisor')));
		$this->assertFalse($ContextUser->has('supervisor'));

		$ContextUser->tearDown();
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testInvalidActory() {
        $ContextUser = $this->starter();

		$ContextUser->has('badActor');

		$ContextUser->tearDown();
	}

	public function starter() {

		/**
		 * A placeholder for PersonCards
		 */
		$obj = new \App\Model\Entity\Member();

		/**
		 * The ContextUser data persisted in the session
		 */
		$stored = [
			'user' => $this->CurrentUser,
			'actorId' => [
				'artist' => 'a-id',
				'manager' => NULL,
				'supervisor' => 's-id'
			],
			'actorCard' => [
				'artist' => NULL,
				'manager' => NULL,
				'supervisor' => $obj
			]
		];

		/**
		 * Configure the Session return data
		 */
		$this->Session->method('read') ->will($this->onConsecutiveCalls(
					 $this->AuthUser, $stored, /*only used by clear() */$this->AuthUser)
				);
		// Only used by clear()
		$this->Session->method('delete')
					 ->will($this->returnValue(TRUE));

		return ContextUser::instance(['session' => $this->Session]);
	}
}

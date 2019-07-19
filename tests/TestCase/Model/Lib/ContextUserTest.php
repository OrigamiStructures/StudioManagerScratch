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
	
	public function setUp() {
		$session = $this->createMock(\Cake\Http\Session::class);
		$this->ContextUser = ContextUser::setSession($session);
	}
	
	public function tearDown() {
//		unset($this->Session);
	}

    /**
     * Test instance method
     *
	 * @expectedException \Cake\Http\Exception\BadRequestException
     * @return void
     */
    public function testInstanceWithNoSessionUser()
    {		
		$ContextUser = ContextUser::instance();
    }

	public function testInstanceWithNoPersistedVersion() 
    {
		$structure = [
			'user' => 'test',
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
		
		$session = $this->createMock(\Cake\Http\Session::class);
		$session->method('read')
             ->will($this->onConsecutiveCalls('test', NULL));

		ContextUser::setSession($session);
		$ContextUser = ContextUser::instance();
		
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
		$obj = new \App\Model\Entity\Member();
		
		$stored = [
			'user' => 'handle',
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
		
		$session = $this->createMock(\Cake\Http\Session::class);
		$session->method('read')
             ->will($this->onConsecutiveCalls('handle', $stored, 'handle'));
		$session->method('delete')
             ->will($this->returnValue(TRUE));

		ContextUser::setSession($session);
		$ContextUser = ContextUser::instance();
		
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
		$obj = new \App\Model\Entity\Member();
		
		$stored = [
			'user' => 'handle',
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
		
		$session = $this->createMock(\Cake\Http\Session::class);
		$session->method('read')
             ->will($this->onConsecutiveCalls('handle', $stored));

		ContextUser::setSession($session);
		return ContextUser::instance();
	}
}

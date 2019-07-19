<?php
namespace App\Model\Lib;

use Cake\ORM\TableRegistry;
use Cake\Http\Session;
use Cake\Http\Exception\BadRequestException;
use App\Model\Lib\CurrentUser;

/**
 * Description of ContextUser
 *
 * @author dondrake
 */
class ContextUser {
		
	protected $defaultValues = [
		'artist' => NULL,
		'manager' => NULL,
		'supervisor' => NULL
	];


	protected $actorId = [
		'artist' => NULL,
		'manager' => NULL,
		'supervisor' => NULL
	];
	
	protected $actorCard = [
		'artist' => NULL,
		'manager' => NULL,
		'supervisor' => NULL
	];
	
	protected $user;
	
	static protected $Session = NULL;
	
	protected $PersonCardsTable = NULL;
	
	static private $instance = NULL;
	
	/**
	 * Construction of the Singleton instance
	 * 
	 * The session-persisted version of ContextUser is only and 
	 * array of data. Those values, if present, are copied into 
	 * this object and the stored copy of ContextUser (self::$instance) 
	 * is made to be a reference to this object that holds it 
	 * (a self-reference in other words)
	 * 
	 * @throws BadRequestException
	 */
	private function __construct($options = null) {
		// session injection was added to allow testing
		$this->setSession($options);
		// currentUser injection was added to allow testing
		$this->setCurrentUser($options);
		
		$contextUser = self::$Session->read($this->cacheKey());
		if (!is_null($contextUser)) {
			foreach(array_keys($contextUser) as $key) {
				$this->$key = $contextUser[$key];
			}
		} else {
			$this->persist();
		}
		self::$instance = $this;
	}
		
	/**
	 * Access point for this Singleton
	 * 
	 * @return ContextUser
	 */
	static public function instance($options = null) {
		if (is_null(self::$instance)) {
			self::$instance = new ContextUser($options);
		}
		return self::$instance;
	}

	/**
	 * Has this actor been defined?
	 * 
	 * @param string $actor
	 * @return boolean
	 */
	public function has($actor) {
		$validActor = $this->validateActor($actor);
		return !is_null($this->actorId[$validActor]);
	}
	
	/**
	 * Identitfy one of the actors
	 * 
	 * This sets the id. The PersonCard will be lazy loaded
	 * 
	 * @param string $actor
	 * @param string|int $id
	 * @return boolean
	 */
	public function set($actor, $id) {
		$validActor = $this->validateActor($actor);
		$this->actorId[$validActor] = $id;
		$this->actorCard[$validActor] = NULL;
		$this->persist();
		return TRUE;
	}
	
	/**
	 * Get the stored actor id or NULL if not set
	 * 
	 * @param type $actor
	 * @return int|string|null
	 */
	public function getId($actor) {
		$validActor = $this->validateActor($actor);
		return $this->actorId[$validActor];
	}
	
	/**
	 * Get the actor's PersonCard or NULL if not set
	 * 
	 * The card is lazy loaded. Only the id is set and stored until 
	 * this request for further data is made
	 * 
	 * @param string $actor
	 * @return PersonCard|null
	 */
	public function getCard($actor) {
		$validActor = $this->validateActor($actor);
		if ($this->has($validActor)) {
			$this->lazyLoadCard($validActor);
		}
		return $this->actorCard[$validActor];
	}
	
	/**
	 * Clear on actor or all values
	 * 
	 * @param type $actor
	 * @return void
	 */
	public function clear($actor = NULL) {
		if (is_null($actor)) {
			self::$Session->delete($this->cacheKey());
			$this->actorId = $this->defaultValues;
			$this->actorCard = $this->defaultValues;
			self::$instance = NULL;
//			self::instance();
		} else {
			$validActor = $this->validateActor($actor);
			$this->actorId[$validActor] = NULL;
			$this->actorCard[$validActor] = NULL;
			$this->persist();
		}
	}
	
// <editor-fold defaultstate="collapsed" desc="AIDS TO TESTING THIS SINGLETON">

	/**
	 * Send in an alternative Persistence object
	 * 
	 * Mocks and testing sessions didn't work. Had to inject objects. 
	 * But now a replacement for Session could be used if need be.
	 * 
	 * @param type $session
	 */
	private function setSession($options) {
		if (isset($options['session'])) {
			self::$Session = $options['session'];
		} else {
			self::$Session = new Session();
		}
	}
	
	private function setCurrentUser($options) {
		if (isset($options['currentUser'])) {
			$this->user = $options['currentUser'];
		} else {
			$currentUser = self::$Session->read('Auth.User');
			if (is_null($currentUser)) {
				$message = 'The user is not logged in';
				throw new BadRequestException($message);
			}
			$this->user = new CurrentUser($currentUser);
		}
	}


	/**
	 * Return the object to uninitialized, non-singleton state
	 * 
	 * This was added to allow testing but might have other uses
	 */
	public function tearDown() {
		$this->actorId = $this->defaultValues;
		$this->actorCard = $this->defaultValues;
		self::$instance = NULL;
		self::$Session = NULL;
		$this->PersonCardsTable = NULL;
	}

	// </editor-fold>
	
// <editor-fold defaultstate="collapsed" desc="PRIVATE METHODS">
	
	private function cacheKey() {
		return "{$this->user->userId()}.ContextUser";
	}
	
	/**
	 * Store the current property values in the Session
	 * 
	 * The relevant properties are stored as an array
	 * 
	 * @throws \App\Model\Lib\Exception
	 */
	private function persist() {
		$data = [
			'user' => $this->user,
			'actorId' => $this->actorId,
			'actorCard' => $this->actorCard,
		];
		try {
			self::$Session->write($this->cacheKey(), $data);
		} catch (Exception $exc) {
			$message = "Couldn't write ContextUser to Session. " . $exc->getMessage();
			throw $exc;
		}
	}

	/**
	 * Convert the string to lower case and verify it is a known value
	 * 
	 * @param type $actor
	 * @return string The validated string in lower case
	 */
	private function validateActor($actor) {
		$validActor = strtolower($actor);
		if (!in_array($validActor, array_keys($this->actorId))) {
			$this->badActor($validActor);
		}
		return $validActor;
	}

	/**
	 * Common Exception point when an invalid actor is referenced
	 * 
	 * @param string $actor
	 * @throws Exception
	 */
	private function badActor($actor) {
		$message = "$actor is not a valid actor focus point. "
				. "Choose actor, manager, or supervisor";
		throw new \BadMethodCallException($message);
	}

	/**
	 * get the PersonCard or load it and return it
	 * 
	 * You won't get here unless the corresponding actor id is set
	 * 
	 * @param string $validActor
	 * @return PersonCard
	 */
	private function lazyLoadCard($validActor) {
		$Card = $this->actorCard[$validActor];
		if (is_null($Card)) {
			$method = "{$validActor}Card";
			$Card = $this->$method();
			$this->persist();
		}
		return $Card;
	}

	/**
	 * get or create a Table instance
	 * 
	 * @return PersoCardsTable
	 */
	private function PersonCardsTable() {
		if (is_null($this->PersonCardsTable)) {
			$this->PersonCardsTable = 
				 TableRegistry::getTableLocator()->get('PersonCards');
		}
		return $this->PersonCardsTable;
	}

	/**
	 * Load and store the artist Person Card
	 */
	private function artistCard() {
		$set = $this->PersonCardsTable()
				->find('stacksFor',
				[
			'seed' => 'identity',

			'ids' => [$this->actorId['artist']]
		]);
		$this->actorCard['artist'] = $set->shift();
	}

	/**
	 * Load and store the artist Supervisor Card
	 */
	private function supervisorCard() {
		$set = $this
				->PersonCardsTable()
				->find('stacksFor',
				[
			'seed' => 'supervisor',

			'ids' => [$this->actorId['supervisor']]
		]);
		$this->actorCard['supervisor'] = $set->shift();
	}

	/**
	 * Load and store the artist Manager Card
	 */
	private function managerCard() {
		$set = $this
				->PersonCardsTable()
				->find('stacksFor',
				[
			'seed' => 'manager',

			'ids' => [$this->actorId['manager']]
		]);
		$this->actorCard['manager'] = $set->shift();
	}

	/**
	 * @return array
	 */
	public function __debugInfo() {
		$actorCard = [];
		foreach (array_keys($this->defaultValues) as $key) {
			$actorCard[$key] = 
					is_null($this->actorCard[$key]) 
					? NULL 
					: 'App\Mode\Entity\PersonCard stack for ' . $this->getCard($key)->name();
		}
		return [
			'user' => 
					is_object($this->user) 
					? "CurrentUser object: {$this->user->name()} {$this->user->userId()}" 
					: 'Not set',
			'actorId' => $this->actorId,
			'actorCard' => $actorCard,
			'Session' => is_object(self::$Session) ? 'Session object' : 'Not set',
			'PersonCardsTable' =>
					is_object($this->PersonCardsTable) 
					? 'PersonCardsTable object' 
					: 'Not set',
			'instance' =>
					is_null(self::$instance) 
					? 'instance is clear' 
					: 'instance is populated'
		];
	}

	// </editor-fold>
	
}

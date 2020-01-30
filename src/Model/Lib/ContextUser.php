<?php
namespace App\Model\Lib;

use App\Model\Entity\PersonCard;
use Cake\ORM\TableRegistry;
use Cake\Http\Session;
use Cake\Http\Exception\BadRequestException;
use App\Model\Lib\CurrentUser;

/**
 * Description of ContextUser
 *
 * @author dondrake
 * @link http://localhost/OStructures/article/currentuser-and-contextuser
 *
 * @property CurrentUser $currentUser
 */
class ContextUser {

    use ActorParamValidator;

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

	protected $currentUser;

	static protected $Session = NULL;

	protected $PersonCardsTable = NULL;

	static private $instance = NULL;

	/**
	 * Construction of the Singleton instance
	 *
	 * The session-persisted version of CurrentUser is only and
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
			$this->set('supervisor', $this->currentUser->supervisorId());
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
     * @todo Should this return some default value if a focus is not set
     *      or should the code do contextUser::has() then deal with
     *      the existance or lack of a focus value?
     *
	 * @param string $actor 'supervisor', 'manager', 'artist'
	 * @return int|string|null
	 */
	public function getId($actor) {
		$validActor = $this->validateActor($actor);
		return $this->actorId[$validActor];
	}

    /**
     * get the specified value from the specified actor's person card
     *
     * @todo do we want this? or should the calling code get a card and
     *      do the work itself?
     * @param $actor
     * @param $property
     */
    public function getProperty($actor, $property)
    {

	}

    /**
     * Is the registered user a superuser?
     *
     * Evaluates the currently logged in user, without regard
     * to any context-aliasing that might be occuring
     *
     * @return bool
     */
    public function isSuperuser()
    {
        return $this->currentUser->isSuperuser();
	}

    /**
     * Is the logged in user operating as a different user?
     *
     * Only superusers have this ability unless we leverage
     * the supervisor alias to allow delegated crud actions.
     *
     * @return bool
     */
    public function isSupervisorAlias()
    {
        return ($this->getId('supervisor') ?? $this->currentUser->userId()) != $this->currentUser->userId();
	}

	/**
	 * Get the actor's PersonCard or NULL if not set
	 *
	 * The card is lazy loaded. Only the id is set and stored until
	 * this request for further data is made
	 *
     * @todo Should this return some default value if a focus is not set
     *      or should the code do contextUser::has() then deal with
     *      the existance or lack of a focus value?
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
			$this->currentUser = $options['currentUser'];
		} else {
			$currentUser = self::$Session->read('Auth.User');
			if (is_null($currentUser)) {
                $message = 'The user is not logged in';
//                $this->Flash->set($message);
//				throw new BadRequestException($message);
			}
			$this->currentUser = new CurrentUser($currentUser);
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


    /**
     * @todo this thing has an id return value hard coded FIX IT!
     * @return string
     */
    public function artistId()
    {
        /** @var PersonCard $personCard */
        $personCard = $this->getCard('artist');
        return $personCard->registeredUserId();
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
                is_object($this->currentUser)
                    ? "CurrentUser object: {$this->currentUser->getName()} {$this->currentUser->userId()}"
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

    // <editor-fold defaultstate="collapsed" desc="PRIVATE METHODS">

	private function cacheKey() {
		return "{$this->currentUser->userId()}.ContextUser";
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
			'user' => $this->currentUser,
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

	// </editor-fold>

}

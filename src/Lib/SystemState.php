<?php
namespace App\Lib;

use App\Lib\RequestUtility;
use Cake\Network\Request;
use Cake\Filesystem\Folder;
//use Cake\Filesystem\File;
use Cake\Collection\Collection;
use App\Lib\StateMap;
use Cake\Event\EventListenerInterface;
//use Cake\Utility\Inflector;
//use App\Model\Table\UsersTable;
use App\Model\Entity\User;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
//use Cake\ORM\Entity;

/**
 * SystemState
 * 
 * This class was designed to be available in all places at all times 
 * in order to make some commonly needed methods (like artistId()) easily 
 * accessible. Also, it overrides Cakes viewVars system which was designed 
 * to carry variables from the Controllers into the View layer. This class 
 * is composed into all standard Cake classes and makes the viewVars 
 * available all over the place.
 * 
 * Besides these basic goals, the class was created with the belief that 
 * the application would use the State design pattern (“Allow an object to alter 
 * its behavior when its internal state changes. The object will appear to 
 * change its class.”). I missunderstood both this application and the 
 * use of the design pattern... so...
 * 
 * @todo There are some static methods that organize things like Edition types 
 *		and Disposition types. These methods could be in a separate class 
 *		with all static methods instead of this mixed bag.
 * @todo Carrying viewVars around like this breaks encapsulation and 
 *		promotes coupling. Not a great idea in retrospect.
 *
 * @author dondrake
 */
class SystemState implements EventListenerInterface {
	
	/**
	 * Controller/action => state map
	 * 
	 * To establish a default state for every callable action
	 *
	 * @var array
	 */
	protected $map;
	
	/**
	 * Cake Request object
	 *
	 * @var Request
	 */
	public $request;
	public static $rq;


	/**
	 * The current system state
	 * 
	 * @var integer
	 */
	protected $_current_state;
	
	/**
	 * A local, independent copy of every variable registered for use on the View
	 *
	 * @var array
	 */
	protected $_viewVars;
	
	protected $_standing_disposition = FALSE;


	/**
	 * Array of types of 'admin' access
	 * 
	 * Methods that test for a kind of admin access need to insure only 
	 * valid admin roles are considered for testing. If the value in question 
	 * is not in this array, just ignore it altogether. (see $this->admin() )
	 *
	 * @var array
	 */
	protected $_admin_roles = [ADMIN_SYSTEM, ADMIN_ARTIST];

	public function __construct(Request $request) {
		$this->request = self::$rq = $request;
		$StateMap = new StateMap();
		$this->map = $StateMap->map;
        $stateChange = NULL;
        if(isset($this->map[$this->request->controller][$this->request->action])){
            $stateChange = $this->map[$this->request->controller][$this->request->action];
        }
        $this->changeState($stateChange);
	}
	
    public function implementedEvents()
    {
        return [
            'Users.Component.UsersAuth.afterLogin' => 'afterLogin',
        ];
    }
	
	/**
	 * Make stored viewVars available
	 * 
	 * @param string $name a valid Hash path
	 * @return mixed
	 */
	public function __get($name) {
		if ($name === 'standing_disposition') {
			return $this->_standingDisposition();
		}
		return isset($this->_viewVars[$name]) ? $this->_viewVars[$name] : null;
	}
	
	/**
	 * See if there is a disposition cached
	 * 
	 * Standing Dispositions may exist by may not have been put into viewVars. 
	 * So in this special case, when there value is requested we'll pull it 
	 * from the cache so it will be available.
	 */
	protected function _standingDisposition() {
		if (!($this->_standing_disposition) && !is_null($this->artistId())) {
			$this->_standing_disposition = Cache::read($this->artistId(), 'dispo');
		}
		return $this->_standing_disposition;
	}

	/**
	 * Is the System State current this $state?
	 * 
	 * @param integer $state 
	 * @return boolean
	 */
	public function is($state) {
		return $this->_current_state == $state;
	}
	
	public function now() {
		return $this->_current_state;
	}

		/**
	 * Set the system state
	 * 
	 * THIS SHOULD SEND A STATECHANGE EVENT
	 * 
	 * @param integer $state
	 */
	public function changeState($state) {
		
		$this->_current_state = $state;
	}
	
	/**
	 * Store the current passes viewVars
	 * 
	 * Controller and View override the normal ViewVars::set to also sent the 
	 * full set of variable here. This makes them available more universally 
	 * (for example, in Tables). Cell and Email classes don't have overrides 
	 * written yet so they don't keep this class up to date.
	 * 
	 * @param array $variables
	 */
	public function storeVars($variables) {
		unset($variables['SystemState']);
		$this->_viewVars = $variables;
	}
	
	/**
	 * Get the logged in artist's ID or the ID of the artist we're mocking
	 * 
	 * Admins (and possibly gallery owners in a later phase) will be able to 
	 * 'act as' an artist rather than only seeing thier own artworks. 
	 * 
	 * CODE IMPROVEMENTS =========================== CODE IMPROVEMENTS
	 * Auth.User.artists [
	 *		'user' => xxx,
	 *		'yyyy' => xxx,
	 * ]
	 * would allow both variations to be found in the same way. 
	 * 'yyyy' could be some random string or a name. id's always hidden at 'xxx'
	 * 
	 * @ticket https://github.com/OrigamiStructures/StudioManagerScratch/issues/66
	 * @ticket https://github.com/OrigamiStructures/StudioManagerScratch/issues/111
	 * @ticket https://github.com/OrigamiStructures/StudioManagerScratch/issues/120
	 * @return string
	 */
	public function artistId($id = NULL) {
        osd(
            'SystemState::artistId() is deprecated. A new method is in development.'
        , 'DEPRECATED');
		if (is_null($id)) {
			return $this->request->session()->read('Auth.User.artist_id');
		}
		$target_artist = FALSE;
		if ($id === 'user') {
			$target_artist = $this->request->session()->read('Auth.User.id');
		} else {
			$ta = $this->request->session()->read("Auth.User.artists.$id");
			$target_artist = is_null($ta) ? FALSE : $ta;
		}
		if ($target_artist) {
			$this->request->session()->write('Auth.User.artist_id', $target_artist);
            $Users = TableRegistry::get('Users');
            $user = new User([
                'id' => $this->request->session()->read('Auth.User.id'),
                'artist_id' => $target_artist
                ]);
            $Users->save($user);
		}
	}
	
	public static function userId() {
		return self::$rq->session()->read('Auth.User.artist_id');
	}

		/**
	 * 
	 * @param type $event
	 */
	public function afterLogin($event) {
//        osd($data, 'the data from after login');
        // die(__LINE__);
		$this->artistId('user');
//		osd($event); die;
	}
    
	/**
	 * Determine the degree (if any) of admin access
	 * 
	 * @param string $type
	 * @return boolean
	 */
	public function admin($type = NULL) {
		$user_role = $this->request->session()->read('Auth.User.role');
		if (is_null($type)) {
			return in_array($user_role, $this->_admin_roles);
		} elseif (in_array($type, $this->_admin_roles)) {
			return strtolower($type) === $user_role;
		}
		return false;
		// Very tentative implementation plan: 
		// 
		// needs to sent TRUE if user is and 'artist' admin, meaning 
		// they need to act as an artist other than themselves. And needs 
		// to return TRUE for both 'system' and 'artist' for developers
	}
	
	/**
	 * Is an ID'd record referenced in the URL arguments?
	 * 
	 * SystemState::hasFocus('artwork', 641)
	 * SystemState::hasFocus('member', 1215)
	 * or
	 * SystemState::hasFocus($artwork) // Artwork entity
	 * SystemState::hasFocus($format) // Format entity
	 * 
	 * @param entity|string $name
	 * @param string $value
	 * @return boolean
	 */
	public function hasFocus($name, $value = NULL) {
		if (is_object($name)) {
			$value = $name->id;
			$name = $this::stripNamespace($name);
		} 
		$result = $this->request->query($name);
		if (!is_null($result)) {
			return $result == $value;
		}
		return FALSE;
	}
	
	/**
	 * Get the class name from a full namespaced name
	 * 
	 * @param Object $obj
	 * @return string
	 */
	static function stripNamespace($obj) {
		$class = explode('\\',get_class($obj));
		return lcfirst(array_pop($class));
	}
	
	/**
	 * Is the value one of the URL query arguements?
	 * 
	 * These are the variables after the '?' in a URL
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function urlArgIsKnown($name) {
		osd('depricated SystemState::urlArgIsKnown()');
		return RequestUtility::urlArgIsKnown($name, $this->request);
//		return !is_null($this->request->query($name));
	}
	
	/**
	 * Return one of the URL query arguements
	 * 
	 * If it doesn't exist, get array of all args
	 * 
	 * @param string $name
	 * @return string|array
	 */
	public function queryArg($name = NULL) {
		osd('depricated SystemState::queryArg()');
		return RequestUtility::queryArg($name, $this->request);
//		if (!is_null($name)) {
//			return $this->request->query($name);
//		} else {
//			return $this->request->query;
//		}	
	}
	
	public function controller() {
		osd('depricated SystemState::controller()');
		return RequestUtility::controller($this->request);
//		return strtolower($this->request->controller);
	}
	
	public function action() {	
		osd('depricated SystemState::action()');
		return RequestUtility::action($this->request);
//		return strtolower($this->request->action);
	}

	/**
	 * Developer Utility to map controller actions
	 * 
	 * Find all the public methods on all controllers and merge the array of 
	 * them with the current SystemState::map then dump this to the screen so 
	 * it can be copied back into the class. This allows full update of the 
	 * map with the current controller/action call points so they can also be 
	 * mapped (while avoiding a lot of hand work).
	 */
	public function inventoryActions() {
		$controller_path = APP . 'Controller';
		$Folder = new Folder($controller_path);
		list( , $contents) = $Folder->read();
		
		$currentEntryPoints = [];
		$classes = new Collection($contents);
			
		$classes->each(function($file_name, $index) use ($controller_path, &$currentEntryPoints) {
			$file = fopen($controller_path . DS . $file_name, 'r');
			$controller = (str_replace('Controller.php', '', $file_name));
			
			while ($line = fgets($file)) {
				if (preg_match('/\t*public *function/', $line)) {
					preg_match('/function *(.*) *\(/', $line, $match);
					$currentEntryPoints[$controller][$match[1]] = NULL;
				}
			}			
			fclose($file);
		});
		
		// This will add in new actions but won't remove retired actions
		foreach($currentEntryPoints as $controller => $actions) {
			foreach($actions as $action => $setting) {
				if (!isset($this->map[$controller][$action])) {
					$this->map[$controller] += [$action => NULL];
				}
			}
		}
		
		$array = var_export($this->map, TRUE);
		$map_class_contents = <<<CLASS
<?php
namespace App\Lib;
class StateMap {
	public \$map = $array;
}
?>
CLASS;
		$map_class_file = fopen(APP . 'Lib' . DS . 'StateMap.php', 'w');
		$result = fwrite($map_class_file, $map_class_contents);
		fclose($map_class_file);
		return $result;
	}
    
    /**
     * Support logic-based referer to suppliment standard request->referer
	 * 
	 * In Controller->_beforeFilter() or a controller action, the 
	 * request->referer() can be examined for key values and 
	 * conditionally included or excluded in SystemState->referer (in Session). 
	 * 
	 * This will allow us to ignore multiple page requests and 
	 * return to some originating page.
	 * 
	 * Calling with a url arguemnt will store that 'referer' and return it
	 * 
	 * Calling with no arguemnt:
	 *	will always return at least the current request->referer but 
	 *	will preferentially return the url that was tucked away in Session
	 * 
	 * Call with (SYSTEM_VOID_REFERER) will return the request->referer 
	 *	and dump the session stored url
	 * 
	 * Call with (SYSTEM_CONSUME_REFERER) will return the session 
	 *	stored url and delete it also
     * 
     * @param string $referer
     * @return string
     */
    public function referer($referer = NULL) {
        $session_referer = $this->request->session()->read('referer');
        if(is_null($referer)){
            $r = (!is_null($session_referer)) ? $session_referer : $this->request->referer();
        } elseif ($referer === SYSTEM_VOID_REFERER) {
            $r = $this->request->referer();
            $this->request->session()->delete('referer');
        } elseif ($referer === SYSTEM_CONSUME_REFERER) {
            $r =  (!is_null($session_referer)) ? $session_referer : $this->request->referer();
            $this->request->session()->delete('referer');
        } else {
            $r = $referer;
            $this->request->session()->write('referer', $referer);
        }
        return $r;
    }
	
	/**
	 * Build a conditions array from query args based on a provided list of arg names
	 * 
	 * The provided list names the args to try to include. The conditions array will 
	 * include values for any entries that did exist.  If the column corresponding to 
	 * an argument is know by a name other than {$arg}_id, the the argument should 
	 * be an array key with a value equal to the column name you need.
	 * <pre>
	 * $this->SystemState->buildConditions(['edition' => 'id', 'artwork'])
	 * yeilds
	 * [
	 *	'id' => '2',
	 *	'artwork_id' => '2',
	 *	'user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2'
	 * ]
	 * 
	 * $this->SystemState->buildConditions(['format' => 'Formats.id'], 'Formats')
	 * yeilds
	 * [
	 *	'Formats.id' => '2',
	 *	'Formats.user_id' => 'f22f9b46-345f-4c6f-9637-060ceacb21b2'
	 * ]
	 * 
	 * </pre>
	 * By default, the user_id value will also be returned in the conditions. 
	 * 
	 * @param array $arg_list
	 * @param boolean $user_filter Should the user_id be added to the condition?
	 * @return array
	 */
	public function buildConditions(array $arg_list, $user_filter = TRUE) {
		$args = array_values($arg_list);
		$keys = array_keys($arg_list);
		$conditions = (new Collection($keys))->reduce(function($accumulate, $key) use($arg_list) {
			if (is_string($key)) {
				$arg = $key;
				$key_name = $arg_list[$key];
			} else {
				$arg = $arg_list[$key];
				$key_name = "{$arg}_id";
			}
			if ($this->urlArgIsKnown($arg)) {
				$accumulate[$key_name] = $this->queryArg($arg);
			}
			return $accumulate;
		}, []);
		if ($user_filter) {
		$table = is_string($user_filter) ? "{$user_filter}." : '' ;
			$conditions["{$table}user_id"] = $this->artistId();
		}
		return $conditions;
	}
}

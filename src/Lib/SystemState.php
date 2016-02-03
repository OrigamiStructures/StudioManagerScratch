<?php
namespace App\Lib;

use Cake\Network\Request;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Collection\Collection;
use App\Lib\StateMap;
use Cake\Event\EventListenerInterface;
use Cake\Utility\Inflector;
use App\Model\Table\UsersTable;
use App\Model\Entity\User;
use Cake\ORM\TableRegistry;

/**
 * Description of SystemState
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
		$this->request = $request;
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
	
	static function limitedEditionTypes() {
		return [EDITION_LIMITED, PORTFOLIO_LIMITED, PUBLICATION_LIMITED];
	}
	
	static function openEditionTypes() {
		return [EDITION_OPEN, PORTFOLIO_OPEN, PUBLICATION_OPEN];
	}

	static function singleFormatEditionTypes() {
		return [EDITION_UNIQUE, EDITION_RIGHTS];
	}
	
	static function multiFormatEditionTypes() {
		return [EDITION_LIMITED, PORTFOLIO_LIMITED, PUBLICATION_LIMITED,
				EDITION_OPEN, PORTFOLIO_OPEN, PUBLICATION_OPEN];
	}

	/**
	 * Make stored viewVars available
	 * 
	 * @param string $name a valid Hash path
	 * @return mixed
	 */
	public function __get($name) {
		return isset($this->_viewVars[$name]) ? $this->_viewVars[$name] : null;
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
	 * @return string
	 */
	public function artistId($id = NULL) {
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
	 * Is the value one of the URL query arguements?
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function isKnown($name) {
		return !is_null($this->request->query($name));
	}
	
	/**
	 * Return one of the URL query arguements
	 * 
	 * @param string $name
	 * @return string
	 */
	public function queryArg($name = NULL) {
		if (!is_null($name)) {
			return $this->request->query($name);
		} else {
			return $this->request->query;
		}	
	}
	
	public function controller() {
		return strtolower($this->request->controller);
	}
	
	public function action() {	
		return strtolower($this->request->action);
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
     * Manage persistent session-based referer
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
			if ($this->isKnown($arg)) {
				$accumulate[$key_name] = $this->queryArg($arg);
			}
			return $accumulate;
		}, []);
		if ($user_filter) {
			$conditions['user_id'] = $this->artistId();
		}
		return $conditions;
	}
}

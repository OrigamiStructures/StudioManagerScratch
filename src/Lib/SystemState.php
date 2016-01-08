<?php
namespace App\Lib;

use Cake\Network\Request;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Collection\Collection;
use App\Lib\StateMap;
use Cake\Event\EventListenerInterface;

define('ARTWORK_CREATE', 1);
define('ARTWORK_REVIEW', 2);
define('ARTWORK_REFINE', 4);
define('ARTWORK_SAVE', 8);

// These will need to be change to something meaningful
// For now, we can act as admins even though we're users
define('ADMIN_SYSTEM', 'user'); // 'admin'
define('ADMIN_ARTIST', 'artist_admin');

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
		$this->changeState($this->map[$this->request->controller][$this->request->action]);
	}
	
    public function implementedEvents()
    {
        return [
            'Users.Component.UsersAuth.afterLogin' => 'afterLogin'
        ];
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
		}
	}
	
	/**
	 * 
	 * @param type $event
	 */
	public function afterLogin($event) {
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
	public function queryArg($name) {
		return $this->request->query($name);
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
}

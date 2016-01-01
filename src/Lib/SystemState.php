<?php
namespace App\Lib;

use Cake\Network\Request;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Collection\Collection;
use App\Lib\StateMap;

define('ARTWORK_CREATE', 1);
define('ARTWORK_REVIEW', 2);
define('ARTWORK_REFINE', 4);

/**
 * Description of SystemState
 *
 * @author dondrake
 */
class SystemState {
	
//	protected $constant = [
//		ARTWORK_CREATE => 'ARTWORK_CREATE',
//		ARTWORK_REVIEW => 'ARTWORK_REVIEW',
//		ARTWORK_REFINE => 'ARTWORK_REFINE',
//		NULL => 'NULL',
//	];

	protected $map;
	protected $request;
	protected $_current_state;
	protected $_viewVars;

	public function __construct(Request $request) {
		$this->request = $request;
		$StateMap = new StateMap();
		$this->map = $StateMap->map;
		$this->_current_state = $this->map[$this->request->controller][$this->request->action];
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

	public function is($state) {
		return $this->_current_state == strtolower($state);
	}
	
	public function now() {
		return $this->_current_state;
	}
	
	public function map() {
		return $this->map;
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
	 * @return string
	 */
	public function artistId() {
		return '1';
	}
	
	/**
	 * Determine the degree (if any) of admin access
	 * 
	 * @param string $type
	 * @return boolean
	 */
	public function admin($type = NULL) {
		// Very tentative implementation plan: 
		// 
		// needs to sent TRUE if user is and 'artist' admin, meaning 
		// they need to act as an artist other than themselves. And needs 
		// to return TRUE for both 'system' and 'artist' for developers
		return TRUE;
	}
	
	
	public function isKnown($name) {
		return !is_null($this->request->query($name));
	}
	
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

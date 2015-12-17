<?php
namespace App\Lib;

use Cake\Network\Request;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Collection\Collection;
use App\Lib\StateMap;

define('ARTWORK_CREATION', 1);
define('ARTWORK_SELECTION', 2);

/**
 * Description of SystemState
 *
 * @author dondrake
 */
class SystemState {
	
	protected $map;
	protected $request;
	protected $_current_state;


	public function __construct(Request $request) {
		$this->request = $request;
		$StateMap = new StateMap();
		$this->map = $StateMap->map;
		$this->_current_state = strtolower($this->map[$this->request->controller][$this->request->action]);
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
	 * Get the logged in artist's ID or the ID of the artist we're mocking
	 * 
	 * Admins (and possibly gallery owners in a later phase) will be able to 
	 * 'act as' an artist rather than only seeing thier own artworks. 
	 * Must return false if not logged in
	 * 
	 * @return string|false
	 */
	public function artistId() {
		return '1';
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
		osd('setting up new state map');
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
		
		$array = var_export($this->map + $currentEntryPoints, TRUE);
		$map_class_contents = <<<CLASS
<?php
namespace App\Lib;
class StateMap {
	public \$map = $array;
}
?>
CLASS;
		$map_class_file = fopen(APP . 'Lib' . DS . 'StateMap.php', 'w');
		osd(fwrite($map_class_file, $map_class_contents), 'result of writing the new map');
		fclose($map_class_file);
	}
}

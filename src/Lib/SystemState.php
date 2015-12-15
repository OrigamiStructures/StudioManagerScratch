<?php
namespace App\Lib;

use Cake\Network\Request;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Collection\Collection;

/**
 * Description of SystemState
 *
 * @author dondrake
 */
class SystemState {
	
	protected $map = array (
  'app' => 
  array (
    'initialize' => NULL,
    'beforeRender' => NULL,
    'artistId' => NULL,
  ),
  'artworks' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
    'sample' => NULL,
    'spec' => NULL,
  ),
  'designs' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'dispositions' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'editions' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
    'spec' => NULL,
  ),
  'formats' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
    'spec' => NULL,
  ),
  'groups' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'groupsmembers' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'images' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'locations' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'members' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'pages' => 
  array (
    'display' => NULL,
  ),
  'pieces' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'series' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'subscriptions' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'users' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
);
	
	public function __construct(Request $request) {
		$this->request = $request;
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
			$controller = strtolower(str_replace('Controller.php', '', $file_name));
			
			while ($line = fgets($file)) {
				if (preg_match('/\t*public *function/', $line)) {
					preg_match('/function *(.*) *\(/', $line, $match);
					$currentEntryPoints[$controller][$match[1]] = NULL;
				}
			}			
		});
		
		echo '<pre>';
		var_export($this->map + $currentEntryPoints);
		echo ';</pre>';
	}

}

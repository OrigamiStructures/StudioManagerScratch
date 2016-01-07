<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use App\Lib\SystemState;
use App\Model\Table\CSTableLocator;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
	
	/**
	 * Class providing system state and artist context
	 *
	 * @var SystemState
	 */
	public $SystemState;
	
	public function __construct(\Cake\Network\Request $request = null,
			\Cake\Network\Response $response = null, $name = null, $eventManager = null,
			$components = null) {
		
		$this->SystemState = new SystemState($request);
		$this->set('SystemState', $this->SystemState);
		$this->locator = new CSTableLocator($this->SystemState);
		$this->tableLocator($this->locator);
		parent::__construct($request, $response, $name, $eventManager, $components);
		
	}
	
	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);
		\Cake\Routing\Router::parseNamedParams($this->request);
	}
	
	public function afterFilter(Event $event) {
		parent::afterFilter($event);
	}

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('CakeDC/Users.UsersAuth');
	}
	
	public function mapStates() {
		$this->set('result', $this->SystemState->inventoryActions());
		$this->set('map', $this->SystemState->map());
		$this->render('/Artworks/mapStates');
	}

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
		$menu = \Cake\ORM\TableRegistry::get('Menus', ['SystemState' => $this->SystemState]);
		$this->set('menus', $menu->assemble());
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }
	
	/**
	 * Override native ViewVarsTrait::set()
	 * 
	 * Maintain a copy of all current variables in the SystemState object
	 * 
	 * @param mixed $name
	 * @param mixed $value
	 * @return object
	 */
    public function set($name, $value = null) {
		$result = parent::set($name, $value);
		$this->SystemState->storeVars($result->viewVars);
		return $result;
	}
	
	public function testMe() {
		$this->c = 0;
		$a = array_fill(0, 10, [$this->call(), 'value', 'other' => 'thing']);
		osd($a);
	}
	
	public function call() {
		return $this->c++;
	}
	
}

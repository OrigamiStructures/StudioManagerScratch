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
use Cake\ORM\TableRegistry;
use App\Lib\SState;
use App\Controller\Component\PieceAllocationComponent;
use Cake\Cache\Cache;

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
		TableRegistry::locator(new CSTableLocator($this->SystemState));
		
		parent::__construct($request, $response, $name, $eventManager, $components);
        $this->eventManager()->on($this->SystemState);
//		$this->eventManager()->on(new SState());
//		$this->SystemState->afterLogin(new Event('thing'));
	}
	
//	public function beforeFilter(Event $event) {
//		parent::beforeFilter($event);
//		\Cake\Routing\Router::parseNamedParams($this->request);
//	}
//	
//	public function afterFilter(Event $event) {
//		parent::afterFilter($event);
//	}

//    public function implementedEvents()
//    {
//		$events = [
////            'Users.Component.UsersAuth.afterLogin' => 'loginListener',
//        ];
//		return array_merge(parent::implementedEvents(), $events);
//    }

	/**
	 * Controller actions to perform on login
	 * 
	 * @param type $event
	 */
//	public function loginListener($event) {
//		
//	}

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
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
		$menu = TableRegistry::get('Menus');
		$this->set('menus', $menu->assemble());
		
		$this->set('standing_disposition', Cache::read($this->SystemState->artistId(), 'dispo'));
		
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
		$art1 = $this->loadComponent('PieceAllocation', ['artwork_id' => 2]);
		osd($art1->stack, 'art1 stack');
//		$art1->initialize(['artwork_id' => 2]);
//		osd($art1);
		
		$ed = 'indexOfEdition';
		$fo = 'indexOfFormat';
//		osd(preg_match('/indexOf(.*)/', $none, $match));
//		osd($match);
//		preg_match('/indexOf(.*)/', $good, $match);
//		osd($match);
		osd($art1->stack->indexOfEdition(6), 'index of edition 6');
		osd($art1->stack->indexOfEdition(2), 'index of edition 1');
		osd($art1->stack->returnEdition(6));
	}
	
}

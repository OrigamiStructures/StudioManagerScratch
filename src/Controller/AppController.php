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

use App\Model\Lib\ContextUser;
use Cake\Controller\Controller;
use Cake\Event\Event;
use App\Lib\SystemState;
use App\Model\Table\CSTableLocator;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use App\Controller\Component\PieceAllocationComponent;
use Cake\Cache\Cache;
use Cake\Controller\Component\PaginatorComponent;
use App\Model\Lib\StackPaginator;
use App\Model\Lib\CurrentUser;

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

	protected $currentUser;

    /**
     * @var ContextUser
     */
	protected $contextUser;

	public function __construct(
	    ServerRequest $request = null,
        Response $response = null,
        $name = null, $eventManager =
        null, $components = null
    ) {

		$this->SystemState = new SystemState($request);
		$this->set('SystemState', $this->SystemState);
		$this->set('SystemState', (new SystemState($request)));

		parent::__construct($request, $response, $name, $eventManager, $components);
	}

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
		$this->loadComponent('Paginator', ['paginator' => new StackPaginator()]);
        $this->loadComponent('Security');
		if($this->Auth->isAuthorized()){
            $this->overrideTableLocator();
        }
		$this->RequestHandler;
	}

	/**
	 * Install a new TableLocator that can inject dependencies
	 *
     * The standard locator limits the config values you can pass.
     * This locator can be configured with values that will be injected
     * into every Table.
     *
     * The new locator allows override and modifications of its
     * stored config injections even after this construction/installation.
     */
	private function overrideTableLocator() {
		TableRegistry::setTableLocator(new CSTableLocator(
				[
					'CurrentUser' => $this->currentUser(),
                    'ContextUser' => $this->contextUser()
				]
			));
		$this->modelFactory('Table', [$this, 'tableFactoryOverride']);
	}

	/**
	 * Fix the fact that default tables didn't use the right locator class
	 *
	 * @todo An issue exists (github) $thisAuthuser doesn't exist in the
	 *      not isset case?
	 *
	 * @param type $modelClass
	 * @return type
	 */
	public function tableFactoryOverride($modelClass, $options = []) {
		return TableRegistry::getTableLocator()->get($modelClass, $options);
	}

	public function currentUser() {
		if (!isset($this->currentUser) && !is_null($this->Auth->user())) {
			$this->currentUser = new CurrentUser($this->Auth->user());
		}
		return $this->currentUser;
	}

    /**
     * @return ContextUser
     */
	public function contextUser() {
		if (!isset($this->contextUser)) {
			$this->contextUser = ContextUser::instance();
		}
		return $this->contextUser;
	}

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        $menus = [];
        if ($this->Auth->isAuthorized()) {
            $menu = TableRegistry::getTableLocator()->get('Menus');
            $menus = $menu->assemble();
        }
        $this->set('menus', $menus);

        if (!array_key_exists('_serialize', $this->viewBuilder()->getVars()) &&
            in_array($this->response->getType(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }

    /**
     * Support logic-based referer to suppliment standard request->referer
     *
     * In Controller->_beforeFilter() or a controller action, the
     * request->referer() can be examined for key values and
     * conditionally included or excluded in a Session.
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
     * @todo Write tests
     *
     * @param string $referer
     * @return string
     */
    public function refererStack($referer = NULL) {
        $session_referer = $this->request->getSession()->read('referer');
        if(is_null($referer)){
            $r = $session_referer ?? $this->request->referer();
        } elseif ($referer === SYSTEM_VOID_REFERER) {
            $r = $this->request->referer();
            $this->request->getSession()->delete('referer');
        } elseif ($referer === SYSTEM_CONSUME_REFERER) {
            $r =  $session_referer ?? $this->request->referer();
            $this->request->getSession()->delete('referer');
        } else {
            $r = $referer;
            $this->request->getSession()->write('referer', $referer);
        }
        return $r;
    }

	public function testMe() {

		$ar = [	1 => ['new' => '', 'old' => 1],
				2 => ['new' => '3', 'old' => 2],
				3 => ['new' => '2', 'old' => 3],
			];

		$result = $ar[3] + $ar[2] + $ar[1];
		extract($result);

		$stuff = [
			function() {
				return $this->request->getQueryParams();
			},
			function($val) {
				return ucwords($val);
			}
		];

		$a1 = ['a', 'b', 'c'];
		$a2 = ['d', 'e', 'f'];
		$a3 = ['a', 'g', 'h', 'i'];

		$combined = array_merge($a1, $a3, $a2);

		$this->set('stuff', $stuff);
		$this->set(compact('new', 'old', 'combined'));



//		osd($new);
//		osd($old);
////		die;
//
//			osd(array_shift($ar));
//			osd(array_shift($ar));
//			osd(array_shift($ar));
////			die;
//		$art1 = $this->loadComponent('PieceAllocation', ['artwork_id' => 2]);
//		osd($art1->stack, 'art1 stack');
////		$art1->initialize(['artwork_id' => 2]);
////		osd($art1);
//
//		$ed = 'indexOfEdition';
//		$fo = 'indexOfFormat';
////		osd(preg_match('/indexOf(.*)/', $none, $match));
////		osd($match);
////		preg_match('/indexOf(.*)/', $good, $match);
////		osd($match);
//		osd($art1->stack->indexOfEdition(6), 'index of edition 6');
//		osd($art1->stack->indexOfEdition(2), 'index of edition 1');
//		osd($art1->stack->returnEdition(6));
	}

}

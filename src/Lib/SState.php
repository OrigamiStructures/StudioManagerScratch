<?php
namespace App\Lib;

use Cake\Event\EventListenerInterface;

class SState implements EventListenerInterface {

	public static $request;
	public static $session;

	public function implementedEvents() {
		return [
			'Controller.startup' => 'startup',
		];
	}

	public function __construct() {

	}

	public function startup($event, $two = '', $three = '') {
		self::$request = $event->subject->request;
		self::$session = $event->subject->request->session();
	}
	
}

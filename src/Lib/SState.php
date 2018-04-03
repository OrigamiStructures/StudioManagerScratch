<?php
namespace App\Lib;

use Cake\Event\EventListenerInterface;

/**
 * This appears to be an attemp to simplify access to request and session 
 * data by making them more global. I'm not sure if there is or was any
 * legitimate reason for trying to do that. And I'm not sure if this 
 * strategy could have worked. But one thing is for sure:
 * 
 * THIS CLASS IS UNUSED. 
 */

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

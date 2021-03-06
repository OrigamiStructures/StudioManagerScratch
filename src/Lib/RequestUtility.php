<?php
namespace App\Lib;

use \Cake\Http\ServerRequest;
/**
 * Description of UrlQuery
 *
 * @author dondrake
 */
class RequestUtility {

	/**
	 * Is the value one of the URL query arguements?
	 *
	 * These are the variables after the '?' in a URL
	 *
	 * @param string $name
     * @param ServerRequest $request
	 * @return boolean
	 */
	public static function urlArgIsKnown($name, $request) {
		return !is_null($request->query($name));
	}

	/**
	 * Return one of the URL query arguements
	 *
	 * If it doesn't exist returns null, get array of all args
	 *
	 * @param string $name
     * @param ServerRequest $request
	 * @return string|null|array
	 */
	public static function queryArg($name = NULL, $request) {
		if (!is_null($name)) {
			return $request->query($name);
		} else {
			return $request->query;
		}
	}

	public static function controller($request) {
		return strtolower($request->controller);
	}

	public static function action($request) {
		return strtolower($request->action);
	}


}

<?php
namespace App\Lib;

/**
 * Description of FGCurl
 *
 * @author dondrake
 */
class FGCurl {
	
	protected $_url = [
		'DevJsonOrder' => "http://dev.ampfg.com/robotOrders/input/json",
		'DevXmlOrder' => "http://dev.ampfg.com/robotOrders/input/xml",
		'DevJsonStatus' => "http://dev.ampfg.com/robotStatus/input/json",
		'DevXmlStatus' => "http://dev.ampfg.com/robotStatus/input/xml",
	];

	public function devJsonOrder($data) {
		return $this->postRequest($data, $this->to('DevJsonOrder'));
	}

	public function devXmlOrder($data) {
		return $this->postRequest($data, $this->to('DevXmlOrder'));
	}

	public function devJsonStatus($data) {
		return $this->postRequest($data, $this->to('DevJsonStatus'));
	}

	public function devXmlStatus($data) {
		return $this->postRequest($data, $this->to('DevXmlStatus'));
	}

	protected function to($destination) {
		return $this->_url[$destination];
	}

	protected function postRequest($data, $url) {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_USERAGENT,
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) '
				. 'Gecko/20080311 Firefox/2.0.0.13');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;

	}
	
}

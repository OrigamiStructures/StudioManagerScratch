<?php

namespace App\Test\TestCase\Lib;

use App\Lib\FGCurl;
use Cake\TestSuite\TestCase;
use App\Test\Fixture\RobotFixture;
use Cake\Utility\Xml;

/**
 * App\Lib\FGCurl Test Case
 */
class FGCurlTest extends TestCase {

	/**
	 * Test subject
	 *
	 * @var \App\Lib\FGCurl
	 */
	public $FGCurl;
	public $RobotFixture;
	public $production = true;
	public $nullTrap = false;

	public function __construct($name = null, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
	}

	/**
	 * If nullTrap is on, check the response and stop null values
	 * 
	 * @param mixed $response
	 * @return boolean
	 */
	public function allowResponse($response) {
		if ($this->nullTrap) {
			return !is_null($response);
		} else {
			return TRUE;
		}
	}

	public function nullTrapMessage() {
		pr("Null trap just surpressed tests.");
	}

	public function servedTestMessage() {
		pr('Production server tests were just surpressed.');
	}

	public function setUp() {
		parent::setUp();
		$this->FGCurl = new FGCurl();
		$this->RobotFixture = new RobotFixture();
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->FGCurl);
		unset($this->RobotFixture);
	}

	/**
	 * Test initial setup
	 *
	 * @return void
	 */


	public function makeXml($data) {
		$body = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				$data,
			]
		];
		$XMLrequest = ['Body' => [$body]];
//        foreach ($data as $index => $response) {
//            $XMLresponse['body'][$index] = $response;
//        }
        $request = XML::fromArray($XMLrequest);
        return [$request->asXML()];


	}
	public function testXmlOrderSingleGood() {

		//setup items
		$items = [
			$this->RobotFixture->orderItemNode['good'][0],
			$this->RobotFixture->orderItemNode['good'][1]
		];

		//setup shipment
		$shipment = $this->RobotFixture->shipmentNode['good'];

		//setup order
		$order = ['Order' => $this->RobotFixture->getOrderNode(TRUE, 0)];
		$order['Order']['OrderItems'] = ['OrderItem' => $items];
		$order['Order']['Shipments'] = $shipment;
		
		//nest into proper array structure
//		$orders = [
//			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
//			'Orders' => [
//				$order
//			]
//		];

		$Xml_order = $this->makeXml($order);
		pr($Xml_order);die;
		pr($this->FGCurl->devXmlOrder($Xml_order));die;

		//dev platform
		$response = Xml::toArray($this->FGCurl->devXmlOrder($Xml_order));
//		$response = Xml::toArray($this->FGCurl->devXmlOrder($Xml_order));

		$this->assertNotNull($response,
				"Test returned NULL response when some "
				. "response was expected from good order on dev server");

		if ($this->allowResponse($response)) {
			foreach ($response as $index => $orderResponse) {
				$this->assertTrue($orderResponse['code'] == 1,
						"Providing good order "
						. "did not return expected order code 1 on dev server");
			}
		} else {
			$this->nullTrapMessage();
		}

		//served platform

		if ($this->production) {
			$response = Xml_decode($this->FGCurl->XmlOrder($Xml_order), true);
//            pr($this->FGCurl->XmlOrder($Xml_order));
			$this->assertNotNull($response,
					"Test returned NULL response when "
					. "some response was expected from good order on production server");
			if ($this->allowResponse($response)) {
				foreach ($response as $index => $orderResponse) {
					$this->assertTrue($orderResponse['code'] == 1,
							"Providing good order "
							. "did not return expected order code 1 production server");
				}
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	
	public function testJsonOrderSingleGood() {

		//setup items
		$items = [
			$this->RobotFixture->orderItemNode['good'][0],
			$this->RobotFixture->orderItemNode['good'][1]
		];

		//setup shipment
		$shipment = $this->RobotFixture->shipmentNode['good'];

		//setup order
		$order = $this->RobotFixture->getOrderNode(TRUE, 0);
		$order['OrderItem'] = $items;
		$order['Shipment'] = $shipment;

		//nest into proper array structure
		$orders = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				$order
			]
		];

		$json_order = [json_encode($orders)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonOrder($json_order), true);

		$this->assertNotNull($response,
				"Test returned NULL response when some "
				. "response was expected from good order on dev server");

		if ($this->allowResponse($response)) {
			foreach ($response as $index => $orderResponse) {
				$this->assertTrue($orderResponse['code'] == 1,
						"Providing good order "
						. "did not return expected order code 1 on dev server");
			}
		} else {
			$this->nullTrapMessage();
		}

		//served platform

		if ($this->production) {
			$response = json_decode($this->FGCurl->JsonOrder($json_order), true);
//            pr($this->FGCurl->JsonOrder($json_order));
			$this->assertNotNull($response,
					"Test returned NULL response when "
					. "some response was expected from good order on production server");
			if ($this->allowResponse($response)) {
				foreach ($response as $index => $orderResponse) {
					$this->assertTrue($orderResponse['code'] == 1,
							"Providing good order "
							. "did not return expected order code 1 production server");
				}
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	public function testJsonOrderMultipleGood() {

		//setup common items
		$items = [
			$this->RobotFixture->orderItemNode['good'][0],
			$this->RobotFixture->orderItemNode['good'][1]
		];

		//setup common shipment
		$shipment = $this->RobotFixture->shipmentNode['good'];

		//setup first order
		$order = $this->RobotFixture->getOrderNode(TRUE, 0);
		$order['OrderItem'] = $items;
		$order['Shipment'] = $shipment;

		$orders = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				$order
			]
		];

		//setup second order
		$order = $this->RobotFixture->getOrderNode(TRUE, 1);
		$order['OrderItem'] = $items;
		$order['Shipment'] = $shipment;

		$orders['Orders'][] = $order;


		$json_order = [json_encode($orders)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonOrder($json_order), true);

		$this->assertNotNull($response,
				"Test returned NULL response when some "
				. "response was expected from good order on dev server");

		if ($this->allowResponse($response)) {
			foreach ($response as $index => $orderResponse) {
				$this->assertTrue($orderResponse['code'] == 1,
						"Providing good order "
						. "did not return expected order code 1 on dev server");
			}
		} else {
			$this->nullTrapMessage();
		}
		//served platform

		if ($this->production) {
			$response = json_decode($this->FGCurl->JsonOrder($json_order), true);
			$this->assertNotNull($response,
					"Test returned NULL response when some "
					. "response was expected from good order on production server");
			if ($this->allowResponse($response)) {
				foreach ($response as $index => $orderResponse) {
					$this->assertTrue($orderResponse['code'] == 1,
							"Providing good order "
							. "did not return expected order code 1 production server");
				}
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	public function testJsonOrderSingleBadOrderNode() {

		//setup common items
		$items = [
			$this->RobotFixture->orderItemNode['good'][0],
			$this->RobotFixture->orderItemNode['good'][1]
		];

		//setup common shipment
		$shipment = $this->RobotFixture->shipmentNode['good'];

		//setup first order
		$order = $this->RobotFixture->getOrderNode(FALSE, 0, 'no_order_reference');
		$order['OrderItem'] = $items;
		$order['Shipment'] = $shipment;

		$orders = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				$order
			]
		];

		$json_order = [json_encode($orders)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonOrder($json_order), true);

		$this->assertNotNull($response,
				"Test returned NULL response when some "
				. "response was expected from providing missing order reference on dev server");

		if ($this->allowResponse($response)) {
			foreach ($response as $index => $orderResponse) {
				$this->assertTrue($orderResponse['code'] == 2004,
						"Providing missing order "
						. "reference did not throw 2004 error on dev server");
			}
		} else {
			$this->nullTrapMessage();
		}
		//served platform

		if ($this->production) {
			$response = json_decode($this->FGCurl->JsonOrder($json_order), true);
			$this->assertNotNull($response,
					"Test returned NULL response when some "
					. "response was expected from providing missing order reference "
					. "on production server");
			if ($this->allowResponse($response)) {
				foreach ($response as $index => $orderResponse) {
					$this->assertTrue($orderResponse['code'] == 2004,
							"Providing missing "
							. "order reference did not throw 2004 error on production server");
				}
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	public function testJsonOrderMultipleBadOrderNode() {

		//setup common items
		$items = [
			$this->RobotFixture->orderItemNode['good'][0],
			$this->RobotFixture->orderItemNode['good'][1]
		];

		//setup common shipment
		$shipment = $this->RobotFixture->shipmentNode['good'];

		//setup first order
		$order = $this->RobotFixture->getOrderNode(FALSE, 0, 'no_order_reference');
		$order['OrderItem'] = $items;
		$order['Shipment'] = $shipment;

		$orders = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				$order
			]
		];

		//setup second order
		$order = $this->RobotFixture->getOrderNode(FALSE, 0, 'no_order_reference');
		$order['OrderItem'] = $items;
		$order['Shipment'] = $shipment;

		$orders['Orders'][] = $order;


		$json_order = [json_encode($orders)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonOrder($json_order), true);

		$this->assertNotNull($response,
				"Test returned NULL response when some "
				. "response was expected from providing missing order reference on dev server");

		if ($this->allowResponse($response)) {
			foreach ($response as $index => $orderResponse) {
				$this->assertTrue($orderResponse['code'] == 2004,
						"Providing missing "
						. "order reference did not throw 2004 error on dev server");
			}
		} else {
			$this->nullTrapMessage();
		}
		//served platform

		if ($this->production) {
			$response = json_decode($this->FGCurl->JsonOrder($json_order), true);
			$this->assertNotNull($response,
					"Test returned NULL response when some "
					. "response was expected from providing missing order reference "
					. "on production server");
			if ($this->allowResponse($response)) {
				foreach ($response as $index => $orderResponse) {
					$this->assertTrue($orderResponse['code'] == 2004,
							"Providing missing "
							. "order reference did not throw 2004 error on production server");
				}
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	/**
	 * @param $error_index the index from the data provider
	 * @param $error_code the error code that should return
	 * @param $error_message the message to show
	 * @dataProvider badItemProvider
	 */
	public function testJsonOrderBadOrderItem($error_index, $error_code,
			$error_message) {

		//setup common items
		$items = [
			$this->RobotFixture->orderItemNode['bad'][$error_index]
		];

		//setup common shipment
		$shipment = $this->RobotFixture->shipmentNode['good'];

		//setup first order
		$order = $this->RobotFixture->getOrderNode(true, 0);
		$order['OrderItem'] = $items;
		$order['Shipment'] = $shipment;

		$orders = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				$order
			]
		];

		$json_order = [json_encode($orders)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonOrder($json_order), true);

		$this->assertNotNull($response,
				"Test returned NULL response when some "
				. "response was expected from $error_message on dev server");

		if ($this->allowResponse($response)) {
			foreach ($response as $index => $orderResponse) {
				$this->assertTrue($orderResponse['code'] == $error_code,
						"$error_message "
						. "did not throw $error_code error on dev server");
			}
		} else {
			$this->nullTrapMessage();
		}
		//served platform

		if ($this->production) {
			$response = json_decode($this->FGCurl->JsonOrder($json_order), true);
//            pr($this->FGCurl->JsonOrder($json_order));
			$this->assertNotNull($response,
					"Test returned NULL response when "
					. "some response was expected from $error_message on production server");
			if ($this->allowResponse($response)) {
				foreach ($response as $index => $orderResponse) {
					$this->assertTrue($orderResponse['code'] == $error_code,
							"$error_message "
							. "did not throw $error_code error on production server");
				}
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	public function badItemProvider() {
		return [
			['bad_customer_item_code', 2002, 'providing bad customer item code'],
			['bad_catalog_id', 2002, 'providing bad catalog id']
//            ['bad_quantity', 2005, 'providing bad quantity']
		];
	}

	public function testDevJsonStatusSingleOrderNumber() {
		//setup items
		$orders = [
			$this->RobotFixture->statusOrderNumbers['good'][0],
		];

		//nest into proper array structure
		$request = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				[
					'order_numbers' => $orders
				]
			]
		];

		$json_order = [json_encode($request)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonStatus($json_order), true);

		$this->assertNotNull($response,
				'Json Status request with one valid job number '
				. 'received a NULL response. Dev.');
		$this->assertTrue(is_array($response),
				'Json Status request with one valid job number '
				. 'didn\'t decode into an array. Dev.');

		if ($this->allowResponse($response)) {
			foreach ($response as $index => $statusResponse) {
				$this->assertTrue($statusResponse['code'] == 1,
						"Json Status request with one valid job number did not "
						. "return an error code of 1. Dev. Received {$statusResponse['code']} "
						. "for job $index");
			}
		} else {
			$this->nullTrapMessage();
		}

		if ($this->production) {
			//dev platform
			$response = json_decode($this->FGCurl->JsonStatus($json_order), true);

			$this->assertNotNull($response,
					'Json Status request with one valid job number '
					. 'received a NULL response. Served');
			$this->assertTrue(is_array($response),
					'Json Status request with one valid job number '
					. 'didn\'t decode into an array. Served.');

			if ($this->allowResponse($response)) {
				foreach ($response as $index => $statusResponse) {
					$this->assertTrue($statusResponse['code'] == 1,
							"Json Status request with one valid job number did not "
							. "return an error code of 1.Served. Received {$statusResponse['code']} "
							. "for job $index");
				}
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	public function testDevJsonStatusSingleReferenceNumber() {
		//setup items
		$orders = [
			$this->RobotFixture->statusOrderReferences['good'][0],
		];

		//nest into proper array structure
		$request = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				[
					'order_references' => $orders
				]
			]
		];

		$json_order = [json_encode($request)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonStatus($json_order), true);

		$this->assertNotNull($response,
				'Json Status request with one valid reference number '
				. 'received a NULL response. Dev.');
		$this->assertTrue(is_array($response),
				'Json Status request with one valid reference number '
				. 'didn\'t decode into an array. Dev.');
		$this->assertCount(1, $response,
				'Good reference number for dev Status '
				. 'check did not return a response.');

		if ($this->allowResponse($response)) {
			foreach ($response as $index => $statusResponse) {
				$this->assertTrue($statusResponse['code'] == 1,
						"Json Status request with one valid reference number did not "
						. "return an error code of 1. Dev. Received {$statusResponse['code']} "
						. "for reference $index");
			}
		} else {
			$this->nullTrapMessage();
		}

		if ($this->production) {
			//hosted platform
			$response = json_decode($this->FGCurl->JsonStatus($json_order), true);

			$this->assertNotNull($response,
					'Json Status request with one valid reference number '
					. 'received a NULL response. Served');
			$this->assertTrue(is_array($response),
					'Json Status request with one valid reference number '
					. 'didn\'t decode into an array. Served.');
			$this->assertCount(1, $response,
					'Good reference number for hosted Status '
					. 'check did not return a response.');

			if ($this->allowResponse($response)) {
				foreach ($response as $index => $statusResponse) {
					$this->assertTrue($statusResponse['code'] == 1,
							"Json Status request with one valid reference number did not "
							. "return an error code of 1.Served. Received {$statusResponse['code']} "
							. "for reference $index");
				}
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	public function testDevJsonStatusMultipleGoodOrderNumbers() {
		//setup items
		$orders = [
			$this->RobotFixture->statusOrderNumbers['good'][0],
			$this->RobotFixture->statusOrderNumbers['good'][1],
		];

		//nest into proper array structure
		$request = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				[
					'order_numbers' => $orders
				]
			]
		];

		$json_order = [json_encode($request)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonStatus($json_order), true);

		$this->assertNotNull($response,
				'Json Status request with multiple valid job numbers '
				. 'received a NULL response. Dev.');
		$this->assertTrue(is_array($response),
				'Json Status request with multiple valid job numbers '
				. 'didn\'t decode into an array. Dev.');
		$this->assertCount(2, $response,
				'Two good order numbers for Dev Status check did not return '
				. 'two responses.');

		if ($this->allowResponse($response)) {
			foreach ($response as $index => $statusResponse) {
				$this->assertTrue($statusResponse['code'] == 1,
						"Json Status request with multiple valid job numbers did not "
						. "return an error code of 1. Dev. Received {$statusResponse['code']} "
						. "for job $index");
			}
		} else {
			$this->nullTrapMessage();
		}

		if ($this->production) {
			//dev platform
			$response = json_decode($this->FGCurl->JsonStatus($json_order), true);

			$this->assertNotNull($response,
					'Json Status request with multiple valid job numbers '
					. 'received a NULL response. Served');
			$this->assertTrue(is_array($response),
					'Json Status request with multiple valid job numbers '
					. 'didn\'t decode into an array. Served.');
			$this->assertCount(2, $response,
					'Two good order numbers for Hosted Status check did not return '
					. 'two responses.');

			if ($this->allowResponse($response)) {
				foreach ($response as $index => $statusResponse) {
					$this->assertTrue($statusResponse['code'] == 1,
							"Json Status request with multiple valid job numbers did not "
							. "return an error code of 1.Served. Received {$statusResponse['code']} "
							. "for job $index");
				}
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	public function testDevJsonStatusMultipleGoodReferenceNumbers() {
		//setup items
		$orders = [
			$this->RobotFixture->statusOrderReferences['good'][0],
			$this->RobotFixture->statusOrderReferences['good'][1],
		];

		//nest into proper array structure
		$request = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				[
					'order_references' => $orders
				]
			]
		];

		$json_order = [json_encode($request)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonStatus($json_order), true);

		$this->assertNotNull($response,
				'Json Status request with multiple valid reference numbers '
				. 'received a NULL response. Dev.');
		$this->assertTrue(is_array($response),
				'Json Status request with multiple valid reference numbers '
				. 'didn\'t decode into an array. Dev.');
		$this->assertCount(2, $response,
				'Two good reference numbers for Dev Status check did not return '
				. 'two responses.');

		if ($this->allowResponse($response)) {
			foreach ($response as $index => $statusResponse) {
				$this->assertTrue($statusResponse['code'] == 1,
						"Json Status request with multiple valid reference numbers did not "
						. "return an error code of 1. Dev. Received {$statusResponse['code']} "
						. "for reference $index");
			}
		} else {
			$this->nullTrapMessage();
		}

		if ($this->production) {
			//hosted platform
			$response = json_decode($this->FGCurl->JsonStatus($json_order), true);

			$this->assertNotNull($response,
					'Json Status request with multiple '
					. 'valid reference numbers received a NULL response. Served');
			$this->assertTrue(is_array($response),
					'Json Status request with multiple '
					. 'valid reference numbers didn\'t decode into an array. Served.');
			$this->assertCount(2, $response,
					'Two good reference numbers for Hosted Status '
					. 'check did not return two responses.');

			if ($this->allowResponse($response)) {
				foreach ($response as $index => $statusResponse) {
					$this->assertTrue($statusResponse['code'] == 1,
							"Json Status request with multiple valid reference numbers did not "
							. "return an error code of 1.Served. Received {$statusResponse['code']} "
							. "for reference $index");
				}
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	public function testDevJsonStatusBadGoodOrderNumbers() {
		//setup items
		$orders = [
			$this->RobotFixture->statusOrderNumbers['bad'][0],
			$this->RobotFixture->statusOrderNumbers['good'][1],
		];

		//nest into proper array structure
		$request = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				[
					'order_numbers' => $orders
				]
			]
		];

		$json_order = [json_encode($request)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonStatus($json_order), true);

		$this->assertNotNull($response,
				'Json Status request with good & bad job numbers '
				. 'received a NULL response. Dev.');
		$this->assertTrue(is_array($response),
				'Json Status request with good & bad job numbers '
				. 'didn\'t decode into an array. Dev.');
		$this->assertCount(2, $response,
				'good & bad order numbers for Dev Status check did not return '
				. 'two responses.');

		if ($this->allowResponse($response)) {
			$keys = array_keys($response);
			$statusResponse = array_shift($response);
			$this->assertTrue($statusResponse['code'] == 3001,
					"Json Status request with good & bad job numbers did not "
					. "return an error code of 3001. Dev. Received {$statusResponse['code']} "
					. "for job $keys[0]");
			$statusResponse = array_shift($response);
			$this->assertTrue($statusResponse['code'] == 1,
					"Json Status request with good & bad job numbers did not "
					. "return an error code of 1. Dev. Received {$statusResponse['code']} "
					. "for job $keys[1]");
		} else {
			$this->nullTrapMessage();
		}

		if ($this->production) {
			//hosted platform
			$response = json_decode($this->FGCurl->JsonStatus($json_order), true);

			$this->assertNotNull($response,
					'Json Status request with good & bad job numbers '
					. 'received a NULL response. Served');
			$this->assertTrue(is_array($response),
					'Json Status request with good & bad job numbers '
					. 'didn\'t decode into an array. Served.');
			$this->assertCount(2, $response,
					'good & bad order numbers for Hosted Status check did not return '
					. 'two responses.');

			if ($this->allowResponse($response)) {
				$keys = array_keys($response);
				$statusResponse = array_shift($response);
				$this->assertTrue($statusResponse['code'] == 3001,
						"Json Status request with good & bad job numbers did not "
						. "return an error code of 3001.Served. Received {$statusResponse['code']} "
						. "for job $keys[0]");
				$statusResponse = array_shift($response);
				$this->assertTrue($statusResponse['code'] == 1,
						"Json Status request with good & bad job numbers did not "
						. "return an error code of 1.Served. Received {$statusResponse['code']} "
						. "for job $keys[1]");
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	public function testDevJsonStatusBadGoodReferenceNumbers() {
		//setup items
		$orders = [
			$this->RobotFixture->statusOrderReferences['bad'][0],
			$this->RobotFixture->statusOrderReferences['good'][1],
		];

		//nest into proper array structure
		$request = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				[
					'order_references' => $orders
				]
			]
		];

		$json_order = [json_encode($request)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonStatus($json_order), true);

		$this->assertNotNull($response,
				'Json Status request with good & bad reference numbers '
				. 'received a NULL response. Dev.');
		$this->assertTrue(is_array($response),
				'Json Status request with good & bad reference numbers '
				. 'didn\'t decode into an array. Dev.');
		$this->assertCount(2, $response,
				'Two good reference numbers for Dev Status check did not return '
				. 'two responses.');

		if ($this->allowResponse($response)) {
			$keys = array_keys($response);
			$statusResponse = array_shift($response);
			$this->assertTrue($statusResponse['code'] == 3001,
					"Json Status request with good & bad reference numbers did not "
					. "return an error code of 3001. Dev. Received {$statusResponse['code']} "
					. "for reference $keys[0]");
			$statusResponse = array_shift($response);
			$this->assertTrue($statusResponse['code'] == 1,
					"Json Status request with good & bad reference numbers did not "
					. "return an error code of 1. Dev. Received {$statusResponse['code']} "
					. "for reference $keys[1]");
		} else {
			$this->nullTrapMessage();
		}

		if ($this->production) {
			//hosted platform
			$response = json_decode($this->FGCurl->JsonStatus($json_order), true);

			$this->assertNotNull($response,
					'Json Status request with good & bad '
					. 'reference numbers received a NULL response. Served');
			$this->assertTrue(is_array($response),
					'Json Status request with good & bad '
					. 'reference numbers didn\'t decode into an array. Served.');
			$this->assertCount(2, $response,
					'good & bad reference numbers for Hosted Status '
					. 'check did not return two responses.');

			if ($this->allowResponse($response)) {
				$keys = array_keys($response);
				$statusResponse = array_shift($response);
				$this->assertTrue($statusResponse['code'] == 3001,
						"Json Status request with good & bad reference numbers did not "
						. "return an error code of 3001.Served. Received {$statusResponse['code']} "
						. "for reference $keys[0]");
				$statusResponse = array_shift($response);
				$this->assertTrue($statusResponse['code'] == 1,
						"Json Status request with good & bad reference numbers did not "
						. "return an error code of 1.Served. Received {$statusResponse['code']} "
						. "for reference $keys[1]");
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	public function testDevJsonStatusMultipleBadOrderNumbers() {
		//setup items
		$orders = [
			$this->RobotFixture->statusOrderNumbers['bad'][0],
			$this->RobotFixture->statusOrderNumbers['bad'][1],
		];

		//nest into proper array structure
		$request = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				[
					'order_numbers' => $orders
				]
			]
		];

		$json_order = [json_encode($request)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonStatus($json_order), true);

		$this->assertNotNull($response,
				'Json Status request with multiple invalid job numbers '
				. 'received a NULL response. Dev.');
		$this->assertTrue(is_array($response),
				'Json Status request with multiple invalid job numbers '
				. 'didn\'t decode into an array. Dev.');
		$this->assertCount(2, $response,
				'Two bad order numbers for Dev Status check did not return '
				. 'two responses.');

		if ($this->allowResponse($response)) {
			foreach ($response as $index => $statusResponse) {
				$this->assertTrue($statusResponse['code'] == 3001,
						"Json Status request with multiple invalid job numbers did not "
						. "return an error code of 3001. Dev. Received {$statusResponse['code']} "
						. "for job $index");
			}
		} else {
			$this->nullTrapMessage();
		}

		if ($this->production) {
			//dev platform
			$response = json_decode($this->FGCurl->JsonStatus($json_order), true);

			$this->assertNotNull($response,
					'Json Status request with multiple invalid job numbers '
					. 'received a NULL response. Served');
			$this->assertTrue(is_array($response),
					'Json Status request with multiple invalid job numbers '
					. 'didn\'t decode into an array. Served.');
			$this->assertCount(2, $response,
					'Two bad order numbers for Hosted Status check did not return '
					. 'two responses.');

			if ($this->allowResponse($response)) {
				foreach ($response as $index => $statusResponse) {
					$this->assertTrue($statusResponse['code'] == 3001,
							"Json Status request with multiple invalid job numbers did not "
							. "return an error code of 3001.Served. Received {$statusResponse['code']} "
							. "for job $index");
				}
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	public function testDevJsonStatusMultipleBadReferenceNumbers() {
		//setup items
		$orders = [
			$this->RobotFixture->statusOrderReferences['bad'][0],
			$this->RobotFixture->statusOrderReferences['bad'][1],
		];

		//nest into proper array structure
		$request = [
			'Credentials' => $this->RobotFixture->getCreds('dev', TRUE),
			'Orders' => [
				[
					'order_references' => $orders
				]
			]
		];

		$json_order = [json_encode($request)];

		//dev platform
		$response = json_decode($this->FGCurl->devJsonStatus($json_order), true);

		$this->assertNotNull($response,
				'Json Status request with multiple invalid reference numbers '
				. 'received a NULL response. Dev.');
		$this->assertTrue(is_array($response),
				'Json Status request with multiple invalid reference numbers '
				. 'didn\'t decode into an array. Dev.');
		$this->assertCount(2, $response,
				'Two bad reference numbers for Dev Status check did not return '
				. 'two responses.');

		if ($this->allowResponse($response)) {
			foreach ($response as $index => $statusResponse) {
				$this->assertTrue($statusResponse['code'] == 3001,
						"Json Status request with multiple invalid reference numbers did not "
						. "return an error code of 3001. Dev. Received {$statusResponse['code']} "
						. "for reference $index");
			}
		} else {
			$this->nullTrapMessage();
		}

		if ($this->production) {
			//hosted platform
			$response = json_decode($this->FGCurl->JsonStatus($json_order), true);

			$this->assertNotNull($response,
					'Json Status request with multiple '
					. 'invalid reference numbers received a NULL response. Served');
			$this->assertTrue(is_array($response),
					'Json Status request with multiple '
					. 'invalid reference numbers didn\'t decode into an array. Served.');
			$this->assertCount(2, $response,
					'Two bad reference numbers for Hosted Status '
					. 'check did not return two responses.');

			if ($this->allowResponse($response)) {
				foreach ($response as $index => $statusResponse) {
					$this->assertTrue($statusResponse['code'] == 3001,
							"Json Status request with multiple invalid reference numbers did not "
							. "return an error code of 3001.Served. Received {$statusResponse['code']} "
							. "for reference $index");
				}
			} else {
				$this->nullTrapMessage();
			}
		} else {
			$this->servedTestMessage();
		}
	}

	public function testDevXmlStatus() {
		$this->markTestIncomplete('Not implemented yet.');
	}
	
	public function d() {
		return array (
    '0' => '<?xml version="1.0" encoding="UTF-8"?>
<Body>
    <Credentials>
        <company>IFOnly American Express City Blitz</company>
        <token>270afe2adbee28b5ffcd87287c5707d66f292d46</token>
    </Credentials>
    <Orders>
        <Order>
            <billing_company>If Only</billing_company>
            <first_name>Celia</first_name>
            <last_name>Peachey</last_name>
            <phone>(518) 256-3396</phone>
            <billing_address>244 Jackson Street, 4th Floor</billing_address>
            <billing_address2/>
            <billing_city>San Francisco</billing_city>
            <billing_state>CA</billing_state>
            <billing_zip>94111</billing_zip>
            <billing_country>US</billing_country>
            <order_reference>order123345</order_reference>
            <note>This is a note for this shipment. It really could be quite a long note.
                It might even have carriage returns.</note>
            <OrderItems>
                <OrderItem>
                    <catalog_id>1602</catalog_id>
                    <customer_item_code/>
                    <name>Test Item #1</name>
                    <quantity>1</quantity>
                </OrderItem>
                <OrderItem>
                    <catalog_id/>
                    <customer_item_code>TestItem2</customer_item_code>
                    <name>Test Item #2</name>
                    <quantity>5</quantity>
                </OrderItem>
            </OrderItems>
            <Shipments>
                <billing>Sender</billing>
                <carrier>UPS</carrier>
                <method>1DA</method>
                <billing_account/>
                <first_name>Jason</first_name>
                <last_name>Tempestini</last_name>
                <email>jason@tempestinis.com</email>
                <phone>925-895-4468</phone>
                <company>Curly Media</company>
                <address>1107 Fountain Street</address>
                <address2/>
                <city>Alameda</city>
                <state>CA</state>
                <zip>94501</zip>
                <country>US</country>
                <tpb_company/>
                <tpb_address/>
                <tpb_city/>
                <tpb_state/>
                <tpb_zip/>
                <tpb_phone/>
            </Shipments>
        </Order>
        <Order>
            <billing_company>If Only</billing_company>
            <first_name>Celia</first_name>
            <last_name>Peachey</last_name>
            <phone>(518) 256-3396</phone>
            <billing_address>244 Jackson Street, 4th Floor</billing_address>
            <billing_address2/>
            <billing_city>San Francisco</billing_city>
            <billing_state>CA</billing_state>
            <billing_zip>94111</billing_zip>
            <billing_country>US</billing_country>
            <order_reference>order123346</order_reference>
            <note>This is a note for this shipment. It really could be quite a long note.
                It might even have carriage returns.</note>
            <OrderItems>
                <OrderItem>
                    <catalog_id>1602</catalog_id>
                    <customer_item_code/>
                    <name>Test Item #1</name>
                    <quantity>10</quantity>
                </OrderItem>
                <OrderItem>
                    <catalog_id/>
                    <customer_item_code>TestItem2</customer_item_code>
                    <name>Test Item #2</name>
                    <quantity>50</quantity>
                </OrderItem>
            </OrderItems>
            <Shipments>
                <billing>Sender</billing>
                <carrier>UPS</carrier>
                <method>1DA</method>
                <billing_account/>
                <first_name>Jason</first_name>
                <last_name>Tempestini</last_name>
                <email>jason@tempestinis.com</email>
                <phone>925-895-4468</phone>
                <company>Curly Media</company>
                <address>1107 Fountain Street</address>
                <address2/>
                <city>Alameda</city>
                <state>CA</state>
                <zip>94501</zip>
                <country>US</country>
                <tpb_company/>
                <tpb_address/>
                <tpb_city/>
                <tpb_state/>
                <tpb_zip/>
                <tpb_phone/>
            </Shipments>
        </Order>
    </Orders>
</Body>
');
	}
}

<?php

namespace App\Test\TestCase\Lib;

use App\Lib\FGCurl;
use Cake\TestSuite\TestCase;
use App\Test\Fixture\RobotFixture;

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
	public function testInitialization() {
		$this->markTestIncomplete('Not implemented yet.');
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

	public function testDevXmlOrder() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testDevJsonStatus() {
//		$response = $this->FGCurl->devJsonStatus($this->jsonStatusRequest());
//		pr(json_decode($response));
////		pr($response);
//		
//		$response = $this->FGCurl->JsonStatus($this->jsonStatusRequest());
//		pr(json_decode($response));
//		pr($response);
	}

	public function testDevXmlStatus() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	protected function jsonOrder() {
		return ['
{
	"Credentials":
		{
			"company":"IfOnly Testing",
			"token":"76be72caa9a4a550ac4593d872f38e0d20618a4a"
		}
	,
	"Orders":
	[
		{
			"billing_company":"If Only",
			"first_name":"Celia",
			"last_name":"Peachey",
			"phone":"518-256-3396",
			"billing_address":"244 Jackson Street, 4th Floor",
			"billing_address2":"",
			"billing_city":"San Francisco",
			"billing_state":"CA",
			"billing_zip":"94111",
			"billing_country":"US",
			"order_reference":"order1233452",
			"note":"This is a note for this shipment. It really could be quite a long note.\n It might even have carriage returns.",
			"OrderItem":
			[
				{
					"catalog_id":"",
					"customer_item_code":"1602",
					"name":"Test Item #1",
					"quantity":"1"
				},
				{
					"catalog_id":"",
					"customer_item_code":"TestItem2",
					"name":"Test Item #2",
					"quantity":"5"
				}
			],
			"Shipment":
				{
					"billing":"Sender",
					"carrier":"UPS",
					"method":"1DA",
					"billing_account":"",
					"first_name":"Jason",
					"last_name":"Tempestini",
					"email":"jason@tempestinis.com",
					"phone":"925-895-4468",
					"company":"Curly Media",
					"address":"1107 Fountain Street",
					"address2":"",
					"city":"Alameda",
					"state":"CA",
					"zip":"94501",
					"country":"US",
					"tpb_company":"",
					"tpb_address":"",
					"tpb_city":"",
					"tpb_state":"",
					"tpb_zip":"",
					"tpb_phone":""
				}
		},

		{
			"billing_company":"If Only",
			"first_name":"Celia",
			"last_name":"Peachey",
			"phone":"518-256-3396",
			"billing_address":"244 Jackson Street, 4th Floor",
			"billing_address2":"",
			"billing_city":"San Francisco",
			"billing_state":"CA",
			"billing_zip":"94111",
			"billing_country":"US",
			"order_reference":"order1233462",
			"note":"This is a note for this shipment. It really could be quite a long note.\n It might even have carriage returns.",
			"OrderItem":
			[
				{
					"catalog_id":"1602",
					"customer_item_code":"",
					"name":"Test Item #1",
					"quantity":"10"
				},
				{
					"catalog_id":"",
					"customer_item_code":"TestItem2",
					"name":"Test Item #2",
					"quantity":"50"
				}
			],
			"Shipment":
				{
					"billing":"Sender",
					"carrier":"UPS",
					"method":"1DA",
					"billing_account":"",
					"first_name":"Jason",
					"last_name":"Tempestini",
					"email":"jason@tempestinis.com",
					"phone":"925-895-4468",
					"company":"Curly Media",
					"address":"1107 Fountain Street",
					"address2":"",
					"city":"Alameda",
					"state":"CA",
					"zip":"94501",
					"country":"US",
					"tpb_company":"",
					"tpb_address":"",
					"tpb_city":"",
					"tpb_state":"",
					"tpb_zip":"",
					"tpb_phone":""
				}
		}

	]
}
'];
	}

	/**
	 * Array nodes for credentials that will succeed
	 */
	protected function goodCreds() {
		return [
			"company" => "IfOnly Testing",
			"token" => "76be72caa9a4a550ac4593d872f38e0d20618a4a"
		];
	}

	/**
	 * Array nodes for credentials that will fail
	 */
	protected function badCreds() {
		return [
			"company" => "Unknown Company",
			"token" => "76be72caa9_bogus_token_872f38e0d20618a4a"
		];
	}

	/**
	 * 
	 * @param string $version 'new', 'existing', 'unknown'
	 * @return string
	 */
	protected function OrderRef($version) {
		switch ($version) {
			case 'new':
				return uniqid();
				break;
			case 'existing':
				$existing_order_ref = $this->exitingOrderRef();
				return 'order1233452';
				break;
			default:
				return 'bad_order_reference';
				break;
		}
	}

	protected function oldOrderRef() {
		
	}

	protected function goodOrder() {
		return [
			// <editor-fold defaultstate="collapsed" desc="basic order data">
			'billing_company' => 'If Only',
			'first_name' => 'Celia',
			'last_name' => 'Peachey',
			'phone' => '518-256-3396',
			'billing_address' => '244 Jackson Street, 4th Floor',
			'billing_address2' => '',
			'billing_city' => 'San Francisco',
			'billing_state' => 'CA',
			'billing_zip' => '94111',
			'billing_country' => 'US',
			'note' => 'This is a note for this shipment. It really could be quite a long note.
	 It might even have carriage returns.',
			// </editor-fold>
			'order_reference' => 'order1233452',
			'OrderItem' => [
				// <editor-fold defaultstate="collapsed" desc="zeroth item">
				0 => [
					'catalog_id' => '',
					'customer_item_code' => '1602',
					'name' => 'Test Item #1',
					'quantity' => '1',
				],
				// </editor-fold>
				// <editor-fold defaultstate="collapsed" desc="first item">
				1 => [
					'catalog_id' => '',
					'customer_item_code' => 'TestItem2',
					'name' => 'Test Item #2',
					'quantity' => '5',
				],
			// </editor-fold>
			],
			// <editor-fold defaultstate="collapsed" desc="Shipment">
			'Shipment' => [
				'billing' => 'Sender',
				'carrier' => 'UPS',
				'method' => '1DA',
				'billing_account' => '',
				'first_name' => 'Jason',
				'last_name' => 'Tempestini',
				'email' => 'jason@tempestinis.com',
				'phone' => '925-895-4468',
				'company' => 'Curly Media',
				'address' => '1107 Fountain Street',
				'address2' => '',
				'city' => 'Alameda',
				'state' => 'CA',
				'zip' => '94501',
				'country' => 'US',
				'tpb_company' => '',
				'tpb_address' => '',
				'tpb_city' => '',
				'tpb_state' => '',
				'tpb_zip' => '',
				'tpb_phone' => '',
			],
				// </editor-fold>
		];
	}

	protected function keyParams() {
		$billing_company = 'If Only';
		$order_reference = 'order1233452';
		$customer_item_codes = [
			'1602',
			'TestItem2',
		];
	}

	protected function customer_order($billing_company, $order_reference) {
		return [
			// <editor-fold defaultstate="collapsed" desc="basic order data">
			'billing_company' => 'If Only',
			'first_name' => 'Celia',
			'last_name' => 'Peachey',
			'phone' => '518-256-3396',
			'billing_address' => '244 Jackson Street, 4th Floor',
			'billing_address2' => '',
			'billing_city' => 'San Francisco',
			'billing_state' => 'CA',
			'billing_zip' => '94111',
			'billing_country' => 'US',
			'note' => 'This is a note for this shipment. It really could be quite a long note.
	 It might even have carriage returns.',
			// </editor-fold>
			'order_reference' => 'order1233452',
		];
	}

	protected function orderItem($item_code) {
		return [
			'catalog_id' => '',
			'customer_item_code' => $item_code,
			'name' => 'Test Item #1',
			'quantity' => '1',
		];
	}

	protected function shipment() {
		return [
			'billing' => 'Sender',
			'carrier' => 'UPS',
			'method' => '1DA',
			'billing_account' => '',
			'first_name' => 'Jason',
			'last_name' => 'Tempestini',
			'email' => 'jason@tempestinis.com',
			'phone' => '925-895-4468',
			'company' => 'Curly Media',
			'address' => '1107 Fountain Street',
			'address2' => '',
			'city' => 'Alameda',
			'state' => 'CA',
			'zip' => '94501',
			'country' => 'US',
			'tpb_company' => '',
			'tpb_address' => '',
			'tpb_city' => '',
			'tpb_state' => '',
			'tpb_zip' => '',
			'tpb_phone' => '',
		];
	}

	protected function badOrder() {
		return [
			// <editor-fold defaultstate="collapsed" desc="basic order data">
			'billing_company' => 'If Only',
			'first_name' => 'Celia',
			'last_name' => 'Peachey',
			'phone' => '518-256-3396',
			'billing_address' => '244 Jackson Street, 4th Floor',
			'billing_address2' => '',
			'billing_city' => 'San Francisco',
			'billing_state' => 'CA',
			'billing_zip' => '94111',
			'billing_country' => 'US',
			'note' => 'This is a note for this shipment. It really could be quite a long note.
	 It might even have carriage returns.',
			// </editor-fold>
			'order_reference' => 'order1233452',
			'OrderItem' => [
				// <editor-fold defaultstate="collapsed" desc="zeroth item">
				0 => [
					'catalog_id' => '',
					'customer_item_code' => '1602',
					'name' => 'Test Item #1',
					'quantity' => '1',
				],
				// </editor-fold>
				// <editor-fold defaultstate="collapsed" desc="first item">
				1 => [
					'catalog_id' => '',
					'customer_item_code' => 'TestItem2',
					'name' => 'Test Item #2',
					'quantity' => '5',
				],
			// </editor-fold>
			],
			// <editor-fold defaultstate="collapsed" desc="Shipment">
			'Shipment' => [
				'billing' => 'Sender',
				'carrier' => 'UPS',
				'method' => '1DA',
				'billing_account' => '',
				'first_name' => 'Jason',
				'last_name' => 'Tempestini',
				'email' => 'jason@tempestinis.com',
				'phone' => '925-895-4468',
				'company' => 'Curly Media',
				'address' => '1107 Fountain Street',
				'address2' => '',
				'city' => 'Alameda',
				'state' => 'CA',
				'zip' => '94501',
				'country' => 'US',
				'tpb_company' => '',
				'tpb_address' => '',
				'tpb_city' => '',
				'tpb_state' => '',
				'tpb_zip' => '',
				'tpb_phone' => '',
			],
				// </editor-fold>
		];
	}

	public function goodOrders() {
		
	}

	protected function jsonStatusRequest() {
		return ['
{
	"Credentials":
{
"company":"Curly Media",
"token":"ac62001e66caaa8614610284b07d50c1a7f487b1"
}
,
	"Orders":
	[
		{
			"order_numbers":
			    [
			    "1902-AEKE",
			    "1902-AEKF",
			    "1902-blah",
			    "1902-foo"
			    ],
			"order_references":
			    [
			    "order15",
			    "order16",
			    "order171"
			    ]
		}
	]
}
'];
	}

}

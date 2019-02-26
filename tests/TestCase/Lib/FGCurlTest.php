<?php

namespace App\Test\TestCase\Lib;

use App\Lib\FGCurl;
use Cake\TestSuite\TestCase;

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

	public function setUp() {
		parent::setUp();
		$this->FGCurl = new FGCurl();
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->FGCurl);
	}

	/**
	 * Test initial setup
	 *
	 * @return void
	 */
	public function testInitialization() {
		$this->markTestIncomplete('Not implemented yet.');
	}

	public function testDevJsonOrder() {
		$response = $this->FGCurl->devJsonOrder($this->jsonOrder());
		pr(json_decode($response));
//		pr($response);
		
		$response = $this->FGCurl->JsonOrder($this->jsonOrder());
		pr(json_decode($response));
//		pr($response);
	}

	public function testDevXmlOrder() {
        $this->markTestIncomplete('Not implemented yet.');
	}

	public function testDevJsonStatus() {
		$response = $this->FGCurl->devJsonStatus($this->jsonStatusRequest());
		pr(json_decode($response));
//		pr($response);
		
		$response = $this->FGCurl->JsonStatus($this->jsonStatusRequest());
		pr(json_decode($response));
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
			'billing_company' => $billing_company,
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
			'order_reference' => $order_reference,
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

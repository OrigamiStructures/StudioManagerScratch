<?php
namespace App\Test\TestCase\Lib;

use App\Lib\FGCurl;
use Cake\TestSuite\TestCase;

/**
 * App\Lib\FGCurl Test Case
 */
class FGCurlTest extends TestCase
{

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
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
	
	public function testDevJsonOrder() {
		$response = $this->FGCurl->devJsonOrder($this->jsonOrder());
		pr(json_decode($response));
	}
	
	public function testDevXmlOrder() {
        $this->markTestIncomplete('Not implemented yet.');
	}
	
	public function testDevJsonStatus() {
        $this->markTestIncomplete('Not implemented yet.');
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
	
	protected function goodCreds() {
		
	}
	
	protected function badCreds() {
		
	}
	
	protected function newOrderRef() {
		return uniqid();
	}
	
	protected function oldOrderRef() {
		
	}
	
	protected function goodOrder() {
		
	}
	
	public function goodOrders() {
		
		
	}
}

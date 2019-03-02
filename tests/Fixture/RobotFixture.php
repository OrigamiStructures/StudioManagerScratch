<?php
/**
 * Created by PhpStorm.
 * User: jasont
 * Date: 2019-03-01
 * Time: 09:25
 */

namespace App\Test\Fixture;


use Cake\TestSuite\Fixture\TestFixture;

class RobotFixture
{
    public $goodCreds =
        [
            "company" => "Curly Media",
            "token" => ''
        ];

    public $badCreds =
        [
            "company" => "CurlyMediaWrong",
            "token" => ''
        ];

    public $orderNode =
        [
            'good' =>
                [
                    [
                        "billing_company" => "Sad New Vistas in Testing",
                        "first_name" => "Jason",
                        "last_name" => "Tempestini",
                        "phone" => "925-895-4468",
                        "billing_address" => "1107 Fountain Street",
                        "billing_address2" => "",
                        "billing_city" => "Alameda",
                        "billing_state" => "CA",
                        "billing_zip" => "94501",
                        "billing_country" => "US",
                        "order_reference" => "",
                        "note" => "This is a note for this shipment. It really could be quite a long note.\n It might even have carriage returns."
                    ],
                    [
                        "billing_company" => "Sad New Vistas in Testing",
                        "first_name" => "Jason",
                        "last_name" => "Tempestini",
                        "phone" => "925-895-4468",
                        "billing_address" => "1107 Fountain Street",
                        "billing_address2" => "",
                        "billing_city" => "Alameda",
                        "billing_state" => "CA",
                        "billing_zip" => "94501",
                        "billing_country" => "US",
                        "order_reference" => "",
                        "note" => "This is a note for this shipment. It really could be quite a long note.\n It might even have carriage returns."
                    ]
                ],
            'bad' =>
                ['no_order_reference' =>
                    [
                        "billing_company" => "Sad New Vistas in Testing",
                        "first_name" => "Jason",
                        "last_name" => "Tempestini",
                        "phone" => "925-895-4468",
                        "billing_address" => "1107 Fountain Street",
                        "billing_address2" => "",
                        "billing_city" => "Alameda",
                        "billing_state" => "CA",
                        "billing_zip" => "94501",
                        "billing_country" => "US",
                        "order_reference" => null,
                        "note" => "This is a note for this shipment. It really could be quite a long note.\n It might even have carriage returns."
                    ]
                ]
        ];

    public $shipmentNode =
        [
            'good' =>
                [
                    "billing" => "Sender",
                    "carrier" => "UPS",
                    "method" => "1DA",
                    "billing_account" => "",
                    "first_name" => "Jason",
                    "last_name" => "Tempestini",
                    "email" => "jason@tempestinis.com",
                    "phone" => "925-895-4468",
                    "company" => "Curly Media",
                    "address" => "1107 Fountain Street",
                    "address2" => "",
                    "city" => "Alameda",
                    "state" => "CA",
                    "zip" => "94501",
                    "country" => "US",
                    "tpb_company" => "",
                    "tpb_address" => "",
                    "tpb_city" => "",
                    "tpb_state" => "",
                    "tpb_zip" => "",
                    "tpb_phone" => ""
                ],
            'bad' =>
                [
                    "billing" => "",
                    "carrier" => "UPS",
                    "method" => "1DA",
                    "billing_account" => "",
                    "first_name" => "Jason",
                    "last_name" => "Tempestini",
                    "email" => "jason@tempestinis.com",
                    "phone" => "925-895-4468",
                    "company" => "Curly Media",
                    "address" => "1107 Fountain Street",
                    "address2" => "",
                    "city" => "Alameda",
                    "state" => "CA",
                    "zip" => "94501",
                    "country" => "US",
                    "tpb_company" => "",
                    "tpb_address" => "",
                    "tpb_city" => "",
                    "tpb_state" => "",
                    "tpb_zip" => "",
                    "tpb_phone" => ""
                ]
        ];

    public $orderItemNode =
        [
            'good' =>
                [
                    [
                        "catalog_id" => "52",
                        "customer_item_code" => "",
                        "name" => "Eucalyptus",
                        "quantity" => "10"
                    ],
                    [
                        "catalog_id" => "",
                        "customer_item_code" => "bc1",
                        "name" => "Ball Cap",
                        "quantity" => "1"

                    ]
                ],
            'bad' =>
                [
                    'bad_catalog_id' =>
                        [
                            "catalog_id" => "bad_catalog_id",
                            "customer_item_code" => "",
                            "name" => "Eucalyptus",
                            "quantity" => "10"

                        ],
                    'bad_customer_item_code' =>
                        [
                            "catalog_id" => "",
                            "customer_item_code" => "bad_customer_item_code",
                            "name" => "Eucalyptus",
                            "quantity" => "10"

                        ],
                    'bad_quantity' =>
                        [
                            "catalog_id" => "52",
                            "customer_item_code" => "",
                            "name" => "Eucalyptus",
                            "quantity" => ""

                        ]
                ]
        ];

    public $statusOrderNumbers =
        [
            'good' =>
                [
                    '1903-AEZY',
                    '1903-AEZW'
                ],
            'bad' =>
                [
                    'bad_order_no',
                    'bad_order_no_2'
                ]
        ];

    public $statusOrderReferences =
        [
            'good' =>
                [
                    '5c7ad48d56750',
                    '5c7ad48e8ee56'
                ],
            'bad' =>
                [
                    'bad_order_ref',
                    'bad_order_ref_2'
                ]
        ];



    public function getCreds($mode, $valid = TRUE)
    {
        if($valid){
            $this->goodCreds['token'] = $this->getToken($mode);
            return $this->goodCreds;
        }else{
            $this->badCreds['token'] = $this->getToken($mode);
            return $this->badCreds;
        }
    }

    /**
     * Return the standard testing token for dev or server environments
     *
     * @param string $mode
     * @return string
     */
    public function getToken($mode = 'Dev')
    {
        if($mode = 'Dev') {
            return 'ac62001e66caaa8614610284b07d50c1a7f487b1';
        } else {
            return 'ac62001e66caaa8614610284b07d50c1a7f487b1';
        }
    }

    public function getOrderNode($valid = TRUE, $index = 0, $variant = '')
    {
        if($valid){
            $this->orderNode['good'][$index]['order_reference'] = uniqid();
            return $this->orderNode['good'][$index];
        } else {
            return $this->orderNode['bad'][$variant];
        }
    }

}
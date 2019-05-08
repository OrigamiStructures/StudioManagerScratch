<?php
 $url = "http://localhost/amp-fg/xmlReplenishments/input";
// $url = "http://localhost/ampfg/xmlReplenishments/input";
 
$post_data = array (
    '0' => '<?xml version="1.0" encoding="UTF-8"?>
<Body><Credentials><company>Sad New Vistas in Testing</company><token>d27889affe5f30432a3723a5214d3d23363e</token></Credentials><Replenishment><ReplenishmentItem><index>0</index><item_id>187</item_id><name>Bag</name><quantity>1</quantity></ReplenishmentItem><ReplenishmentItem><index>1</index><item_id>141</item_id><name>Bag-O-Rocks</name><quantity>1</quantity></ReplenishmentItem><ReplenishmentItem><index>2</index><item_id>140</item_id><name>Goat</name><quantity>1</quantity></ReplenishmentItem></Replenishment></Body>
');
 
// $post_data = array (
//     0 => '');
 
$ch = curl_init();
 
curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
 
curl_setopt($ch, CURLOPT_URL, $url);
 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// we are doing a POST request
curl_setopt($ch, CURLOPT_POST, 1);
// adding the post variables to the request
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
 
$output = curl_exec($ch);
 
curl_close($ch);
 
echo $output;
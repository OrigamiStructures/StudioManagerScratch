<?php
//dev
$url = "http://dev.ampfg.com/robotStatuses/input/json";

//external
// $url = "https://fg.ampprinting.com/robotStatuses/input/json";
 
$post_data = ['
{
	"Credentials":
		{
			"company":"Curly Media",
			"token":"146567403f8aadb4bbd468b9aa7879742704c2ca"
		}
	,
	"Orders":
	[
		{
			"order_numbers":
			    [
			    "1902-AEEM",
			    "1902-AEEN",
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
?>
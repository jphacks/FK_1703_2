<?php
$service_url = 'https://kgsearch.googleapis.com/v1/entities:search';
$api_key="AIzaSyAa48vTShzI501hNqTCvned8iRh3-S_S_Q";
$query="カプチーノ";
$params = array(
  'query' => $query,
  'limit' => 3,
  'indent' => TRUE,
  'key' => $api_key);
$url = $service_url . '?' . http_build_query($params);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
$result = json_decode($response, true);
curl_close($curl);

$keys = array("楽しい");

foreach((array)$result['itemListElement'] as $element) {
  $keys[] = $element['result']['name'];
}

for($i=1;$i<=3;$i++)
{
	if(strcasecmp($query,$keys[$i])!=0)
	{
		$key = $keys[$i];
		break;
	}
	$key= "カプチーノ";
}

echo $key;

?>
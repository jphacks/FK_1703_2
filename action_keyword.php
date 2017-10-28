<?php

$keyword = $_POST['keyword'];

//$keyword = "ハッカソン";

$suggest_data = array(
  'qu' => $keyword .'+',
  'hl' => 'ja',
  'output' => 'toolbar',
  'ie' => 'utf_8',
  'oe' => 'utf_8'
);

$suggest_url = 'http://suggestqueries.google.com/complete/search' . '?' . http_build_query($suggest_data);

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $suggest_url);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
$suggest_result_xml = curl_exec($ch);
$xml = simplexml_load_string($suggest_result_xml);
$suggest_result_json = json_encode($xml,JSON_UNESCAPED_UNICODE);
curl_close($ch);

$suggest_result_data = json_decode($suggest_result_json,true);

$suggest_keywords=array("楽しい");

foreach((array)$suggest_result_data['CompleteSuggestion'] as $suggest_data)
{
 	$suggest_keywords[] = $suggest_data['suggestion']['@attributes']['data'];
}

for($i=1;$i<=10;$i++)
{
	$suggest_keyword[$i]=str_replace($keyword, "", $suggest_keywords[$i]);
}

$rand_keys = array_rand($suggest_keyword,10);

$send_keywords['relation_keyword'] = array("");
$send_number = 0;


for($i=0;$i<10;$i++)
{
	if(mb_strlen($suggest_keyword[$rand_keys[$i]]) <= 15)
	{
		$send_keywords['relation_keyword'][] = $suggest_keyword[$rand_keys[0]];
		$send_number++;
		if($send_number == 1)
		{
			break;
		}
	}
	else
	{
		continue;
	}
}

$send_keywords['relation_keyword'][] = $suggest_keyword[$rand_keys[1]];

$limit_number=3;

$graph_data = array(
  'query' => $keyword,
  'limit' => $limit_number,
  'indent' => TRUE,
  'key' => 'AIzaSyAa48vTShzI501hNqTCvned8iRh3-S_S_Q'
);

$graph_url = 'https://kgsearch.googleapis.com/v1/entities:search' . '?' . http_build_query($graph_data);

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $graph_url);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
$graph_result_json = curl_exec($ch);
$graph_result_data = json_decode($graph_result_json,true);
curl_close($ch);

$graph_keywords = array("楽しい");


if($graph_result_data['itemListElement'] == null)
{
	if(mb_strlen($suggest_keyword[$rand_keys[9]]) <= 15)
	{
		$send_keywords['relation_keyword'][] = $suggest_keyword[$rand_keys[9]];
	}
	else
	{
		$send_keywords['relation_keyword'][] = "カプチーノ";
	}
}
else
{
	foreach((array)$graph_result_data['itemListElement'] as $graph_data)
	{
 		$graph_keywords[] = $graph_data['result']['name'];
	}

	for($i=1;$i<=$limit_number;$i++)
	{
		if( strcasecmp($keyword,$graph_keywords[$i]) != 0 )
		{
			$graph_key = $graph_keywords[$i];
			break;
		}
		$graph_key= "カプチーノ";
	}
	$send_keywords['relation_keyword'][] = $graph_key;
}

$relation_keyword_split = array_splice($send_keywords['relation_keyword'],0,1);

$send_keywords_json = json_encode($send_keywords,JSON_UNESCAPED_UNICODE);

echo $send_keywords_json;

?>

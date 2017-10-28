<?php

$keyword_number = 1;	//取得するキーワード数

$title = "";	//タイトル

$body= "";	//本文

$keyword_data = array(	
	'app_id' => 'c33b6b99123f6b322e73a3f8c8047ac46410c010100a533f2afdda752bd5800f',
	'title' => $title,
	'body' => $body,
	'max_num' => $keyword_number
);

$keyword_data_json = json_encode($keyword_data,JSON_UNESCAPED_UNICODE);

$ch = curl_init();
curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'POST');
curl_setopt($ch,CURLOPT_POSTFIELDS,$keyword_data_json);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_URL,'https://labs.goo.ne.jp/api/keyword');
$keyword_result_json=curl_exec($ch);
curl_close($ch);

$keyword_result_data = json_decode($keyword_result_json,true);

for($i=0;$i<$keyword_number;$i++)
{
	if(isset($keyword_result_data['keywords'][$i]))
	{
 		foreach($keyword_result_data['keywords'][$i] as $key => $value)
		{
			$keyword = $key;
		}
	}
	else
	{
		$keyword = "カプチーノ";
	}
}

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

foreach((array)$graph_result_data['itemListElement'] as $graph_data) {
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

$send_keywords = array($graph_key);

echo $send_keywords;
?>
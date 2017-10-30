<?php

$text = $_POST['sentence'];

$target = "en";

$translate_data = array(
	'text' => $text,
	'target' => $target
);

if(mb_strlen($text) >= 1000)
{
	echo "文字数を超えています";
}
else if(mb_strlen($text) == 0)
{
	echo "文字数が足りません";
}
else
{	
	$translate_url = 'http://34.213.207.237:8080/translate' . '?' . http_build_query($translate_data);

	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$translate_url);
	curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'GET');
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	$translate_result_text = curl_exec($ch);
	curl_close($ch);
	
	$send_translate['text'] = $translate_result_text;

	$send_translate_json = json_encode($send_translate,JSON_UNESCAPED_UNICODE);

	echo $send_translate_json;
}

?>

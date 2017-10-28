<?php

require __DIR__ . '/vendor/autoload.php';

# Imports the Google Cloud client library
use Google\Cloud\Speech\SpeechClient;

# Your Google Cloud Platform project ID
$projectId = 'for-grandea';
    
putenv("GOOGLE_APPLICATION_CREDENTIALS=/Library/WebServer/Documents/speech/for Grandea-3a9c5701afe9.json");
    
date_default_timezone_set('Asia/Tokyo');

# Instantiates a client
$speech = new SpeechClient([
    'projectId' => $projectId,
    'languageCode' => 'ja-JP',
]);

# The name of the audio file to transcribe
$fileName = __DIR__ . '/resources/tenki.wav';

# The audio file's encoding and sample rate
$options = [
    'encoding' => 'LINEAR16',
    'sampleRateHertz' => 16000,
];

# Detects speech in the audio file
$results = $speech->recognize(fopen($fileName, 'r'), $options);

#foreach ($results as $result) {
    #echo 'Transcription: ' . $result->alternatives()[0]['transcript'] . PHP_EOL;
#}

# [END speech_quickstart]
echo json_encode($results);

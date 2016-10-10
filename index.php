<?php

use MonkeyLearn\Client as MonkeyLearn;
use Codebird\Codebird;

require 'vendor/autoload.php';

$ml = new MonkeyLearn('c4dc76753fcfb357591f3be15f7a1c406e836d48');

Codebird::setConsumerKey('R1rdPOe1AglckiQ3T1fWeNvDB','1URDAsrnMzPLzst8vBHp4OLVyBIj0XbbG0ch3hfGI20hzRQX3C');

$cb = Codebird::getInstance();
$cb->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);

$cb->setToken('785330899177000960-tZtzLNpTcRcmN8eXPLudxMp7MCVDxYV','jZvWgpD19W5rJtruW66I4szd5kSsN9uZVqXokRaFCrWqP');

$mentions = $cb->statuses_mentionsTimeline();

if (!isset($mentions[0])){
	return;
}

$tweets = [];

foreach ($mentions as $index => $mention){
	if (isset($mention['id'])){
		$tweets[] =[
		'id' => $mention['id'],
		'user_screen_name' => $mention['user']['screen_name'],
		'text' => $mention['text'],
		];
	}
}

$tweetsText = array_map(function ($tweet) {
	return $tweet['text'];
},$tweets);

$positiveEmojis = [
    '&#x1F601;',
    '&#x1F602;',
    '&#x1F603;',
    '&#x1F604;',
    '&#x1F605;',
    '&#x1F606;',
    '&#x1F609;',
    '&#x1F60A;',
    '&#x1F60C;',
];

$neutralEmojis = [
    '&#x1F610;',
    '&#x1F611;',
    '&#x1F636;',
];

$negativeEmojis = [
    '&#x1F612;',
    '&#x1F613;',
    '&#x1F614;',
    '&#x1F61E;',
    '&#x1F622;',
    '&#x1F623;',
    '&#x1F625;',
    '&#x1F629;',
    '&#x1F62A;',
];

$analysis = $ml->classifiers->classify('cl_qkjxv9Ly', $tweetsText, true);

foreach ($tweets as $index => $tweet){
	switch(strtolower($analysis->result[$index][0]['label'])){
		case 'positive':
			$emojiSet = $positiveEmojis;
			break;
		case 'negative':
			$emojiSet = $negativeEmojis;
			break;
		case 'neutral':
			$emojiSet = $neutralEmojis;
			break;
	}
	
	//tweet the user
	//track
	$tracking_file = "tracking.log";
	$fh = fopen($tracking_file, 'a') or die("can't open tracking file");
	fwrite($fh, $tweets[$index]['id']."\n");
	fclose($fh);
	
}
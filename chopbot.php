<?php

use MonkeyLearn\Client as MonkeyLearn;
use Codebird\Codebird;

require 'vendor/autoload.php';

$ml = new MonkeyLearn('c4dc76753fcfb357591f3be15f7a1c406e836d48');

Codebird::setConsumerKey('R1rdPOe1AglckiQ3T1fWeNvDB','1URDAsrnMzPLzst8vBHp4OLVyBIj0XbbG0ch3hfGI20hzRQX3C');

$cb = Codebird::getInstance();
$cb->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);

$cb->setToken('785330899177000960-tZtzLNpTcRcmN8eXPLudxMp7MCVDxYV','jZvWgpD19W5rJtruW66I4szd5kSsN9uZVqXokRaFCrWqP');

//rename to the twitter handle of the bot:
$bot_name = "@chop_bot";
//file for tracking which tweets we have replied to:
$tracking_file = "tracking.log";
//get the id of the last replied to tweet from the tracking file:
$fa = file($tracking_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
rsort($fa);
//Latest tweet:
//echo $fa[0];
//get new mentions since the last replied id
$mentions = $cb->statuses_mentionsTimeline($fa[0] ? 'since_id=' . $fa[0] : '');
if (!isset($mentions[0])){
	//return;
	die("uh-oh! something went wrong.");
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
	//read tweet into array. add a space at the end for short repetitions.
	$chop=explode(' ',strtolower($tweet['text']));
	$chop[count($chop)-1] .= ' ';
	//remove bots' name from the array
	foreach ($chop as $i=>$slice){
		if (!strcmp($slice,strtolower($bot_name))){
			unset($chop[$i]);
		}
	}
	$min_repeat_times = 1;
	$max_repeat_times = 6;
	//check if the tweet is long enough to operate on:
	if (count($chop)>2){
		shuffle($chop);
		//repeat a section at random:
		$slice_start = rand(0,count($chop)-1);
		$slice_stop = rand($slice_start, count($chop)+$slice_start);
		//echo("slice_start:".$slice_start."\n");
		//echo("slice_stop:".$slice_stop."\n");
		$sliced = array_slice($chop, 0, $slice_start, true);	
		for ($i = 0; $i <= rand($min_repeat_times,$max_repeat_times); $i++){
			$sliced = array_merge($sliced,array_slice($chop, $slice_start,$slice_stop, true));
		}
		$sliced = array_merge($sliced, array_slice($chop, $slice_stop, count($chop)-1, true));
		//form the word array back into a string, ready to tweet.
		$chop_output=implode(' ',$sliced);
		
	} else {
		//otherwise just repeat the word a bunch of times.
		$chop_output = implode(' ',$chop);
		$chop_output = str_repeat($chop_output, rand($min_repeat_times,$max_repeat_times));
	}
	//add username, trim to 140 chars and remove last word due incase it's cut off.
	$preg_pattern = '/.*(?=\s)/';
	preg_match ($preg_pattern,substr('@' . $tweet['user_screen_name'] . ' ' . $chop_output,0,138),$preg_output);
	
	//append the emoji - cherry on top.
	$tweet_output = $preg_output[0] . ' ' . html_entity_decode($emojiSet[rand(0,count($emojiSet)-1)], 0, 'UTF-8');
	
	//$tweet_output = substr('@' . $tweet['user_screen_name'] . ' ' . $chop_output,0,138) . ' ' . html_entity_decode($emojiSet[rand(0,count($emojiSet)-1)], 0, 'UTF-8');
	
	// echo the tweet before sending it:
	echo ($tweet_output."\n");
	
	//tweet the user
	$cb->statuses_update([
		'status' => $tweet_output,
		'in_reply_to_status_id' => $tweet['id'],
	]);
	
	//add the tweets we just replied to in the tracking log:
	$fh = fopen($tracking_file, 'a') or die("can't write to tracking file");
	fwrite($fh, $tweets[$index]['id']."\n");
	fclose($fh);
}
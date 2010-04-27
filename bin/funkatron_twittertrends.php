#!/usr/bin/php
<?php
$url = 'http://search.twitter.com/trends.json';

$json = file_get_contents($url);

//echo "<pre>"; echo print_r($json, true); echo "</pre>";

if (!$json) {
	trigger_error("getting trends data failed -- exiting", E_USER_ERROR);
}

$trends = json_decode($json);

$rank = count($trends->trends);
foreach($trends->trends as &$trend) {
	$trend->as_of = $trends->as_of;
	$trend->rank = $rank--;
	$trend->unixtime = strtotime($trend->as_of);
}

/*
	We need to make an object and assign the new docs to a property called "docs"
*/
$newdocs->docs = $trends->trends;

$newjson = json_encode($newdocs);

//echo "<pre>"; echo print_r($newjson, true); echo "</pre>";

$cdb_url = 'http://127.0.0.1:5984/twitter_trends/_bulk_docs';

$http = new HttpRequest($cdb_url, HttpRequest::METH_POST);
$http->setContentType('application/json');
$http->setRawPostData($newjson);
$http->send();
//echo "<pre>"; echo print_r($http->getResponseMessage(), true); echo "</pre>";
// echo "<pre>"; echo print_r($newjson, true); echo "</pre>";

//exit();

// update views
$url = 'http://127.0.0.1:5984/twitter_trends/_view/rank/byminute?group=true';
$json_counts = file_get_contents($url);
$url = 'http://127.0.0.1:5984/twitter_trends/_view/rank/byhour?group=true';
$json_counts = file_get_contents($url);
$url = 'http://127.0.0.1:5984/twitter_trends/_view/rank/byday?group=true';
$json_counts = file_get_contents($url);
$url = 'http://127.0.0.1:5984/twitter_trends/_view/rank/bydayofweek?group=true';
$json_counts = file_get_contents($url);
$url = 'http://127.0.0.1:5984/twitter_trends/_view/rank/bymonth?group=true';
$json_counts = file_get_contents($url);
// 
// $counts = json_decode($json_counts);
// 
// foreach($counts->rows as &$row) {
// 	$row->key = strip_tags(stripslashes($row->key));
// 	// echo "<pre>"; echo print_r($row->key, true); echo "</pre>";
// }
// 
// // echo "<pre>"; echo print_r($counts, true); echo "</pre>";
// 
// $json_counts = json_encode($counts);
// 
// // echo "<pre>"; echo print_r($json_counts, true); echo "</pre>";
// 
// 
// // $json_counts = strip_tags(stripslashes($json_counts));
// 
// file_put_contents('/var/www/twittersource.info/htdocs/twitter-trends.json', $json_counts);



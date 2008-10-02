#!/usr/bin/php
<?php


$url = 'http://twitter.com/statuses/public_timeline.json';
// $url = 'public_timeline.json';

$json = file_get_contents($url);

if (!$json) {
	trigger_error("getting public timeline failed -- exiting", E_USER_ERROR);
}

$pubtweets->docs = json_decode($json);

$newjson = json_encode($pubtweets);
// $json = json_encode(json_decode($json));

// echo "<pre>"; echo print_r($json, true); echo "</pre>";

$cdb_url = 'http://127.0.0.1:5984/publictweets/_bulk_docs';

$http = new HttpRequest($cdb_url, HttpRequest::METH_POST);
$http->setContentType('application/json');
$http->setRawPostData($newjson);
$http->send();

// update view
$url = 'http://127.0.0.1:5984/publictweets/_view/sourcetotal/total?group=true';
$json_counts = file_get_contents($url);

$counts = json_decode($json_counts);

foreach($counts->rows as &$row) {
	$row->key = strip_tags(stripslashes($row->key));
	// echo "<pre>"; echo print_r($row->key, true); echo "</pre>";
}

// echo "<pre>"; echo print_r($counts, true); echo "</pre>";

$json_counts = json_encode($counts);

// echo "<pre>"; echo print_r($json_counts, true); echo "</pre>";


// $json_counts = strip_tags(stripslashes($json_counts));

file_put_contents('/var/www/funkatron.com/htdocs/twitter-source-stats.json', $json_counts);

// echo "<pre>"; echo print_r($http->getResponseMessage(), true); echo "</pre>";
// echo "<pre>"; echo print_r($json, true); echo "</pre>";

#!/usr/bin/php
<?php
$url = 'http://twitter.com/statuses/public_timeline.json';

$json = file_get_contents($url);

if (!$json) {
	trigger_error("getting public timeline failed -- exiting", E_USER_ERROR);
}

$pubtweets->docs = json_decode($json);

foreach ($pubtweets->docs as &$doc) {
	$newdoc->_id = $doc->id;
	$newdoc->date = strtotime($doc->created_at);
	$newdoc->source = $doc->source;
	$doc = $newdoc;
	unset($newdoc);
}

$newjson = json_encode($pubtweets);

$cdb_url = 'http://127.0.0.1:5984/publictweets_sources/_bulk_docs';

$http = new HttpRequest($cdb_url, HttpRequest::METH_POST);
$http->setContentType('application/json');
$http->setRawPostData($newjson);
$http->send();

/*
	update view
*/
// $url = 'http://127.0.0.1:5984/publictweets_sources/_view/sourcetotal/total?group=true';
$url = 'http://127.0.0.1:5984/publictweets_sources/_view/all/counts?group=true';
$json_counts = file_get_contents($url);

$counts = json_decode($json_counts);

foreach($counts->rows as &$row) {
	$row->key = strip_tags(stripslashes($row->key));
}


$json_counts = json_encode($counts);

if ($json_counts) {
	file_put_contents('/var/www/funkatron.com/htdocs/twitter-source-stats.json', $json_counts);
}